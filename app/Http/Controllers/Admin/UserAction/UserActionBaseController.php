<?php


namespace App\Http\Controllers\Admin\UserAction;


use App\Common\Enums\AdvAliasEnum;
use App\Common\Tools\CustomException;
use App\Datas\N8GlobalUserData;
use App\Http\Controllers\Admin\BaseController;
use App\Services\ConvertCallbackMapService;
use App\Services\CustomConvertCallbackMapService;
use Illuminate\Support\Facades\DB;

class UserActionBaseController extends BaseController
{

    /**
     * @var string
     * 默认排序字段
     */
    protected $defaultOrderBy = 'action_time';


    public function selectFilter($unionUserId = 'uuid'){

        $this->curdService->selectQueryBefore(function() use ($unionUserId){
            $this->curdService->customBuilder(function ($builder) use ($unionUserId) {
                $requestData = $this->curdService->requestData;
                $unionUserQuery = DB::table('n8_union_users');

                $isSelf = $requestData['is_self'] ?? 1;
                $isSelf && $unionUserQuery->where('admin_id', $this->adminUserService->readId());

                !empty($requestData['admin_id']) && $unionUserQuery->where('admin_id', $requestData['admin_id']);

                if (!$this->adminUserService->isAdmin()) {
                    $adminIds = $this->adminUserService->getChildrenAdminIds();
                    $unionUserQuery->whereIn('admin_id', $adminIds);
                }

                !empty($requestData['channel_id']) && $unionUserQuery->where('channel_id', $requestData['channel_id']);
                !empty($requestData['user_type']) && $unionUserQuery->where('user_type', $requestData['user_type']);
                !empty($requestData['created_time']) && $unionUserQuery->whereBetween('created_time', $requestData['created_time']);

                if ($unionUserQuery !== DB::table('n8_union_users')) {
                    $unionUser = $unionUserQuery->select('id');
                    $builder->LeftjoinSub($unionUser, 'union_user', function ($join) use ($unionUserId) {
                        $tableName = $this->model->getTable();
                        $join->on($tableName . '.' . $unionUserId, '=', 'union_user.id');
                    });
                }
            });
        });
    }

    // 广告维度筛选
    public function selectFilterAdv($convertType,$convertId = 'id',$clickField = 'click_id'){
        $this->curdService->selectQueryBefore(function () use ($convertId,$convertType,$clickField){
            $this->curdService->customBuilder(function ($builder) use ($convertId,$convertType,$clickField) {

                $requestData = $this->curdService->requestData;

                if(!empty($requestData['unit_id']) ||  !empty($requestData['convert_callback_status'])){

                    if(empty($requestData['adv_alias'])){
                        throw new CustomException([
                            'code' => 'NOT_ADV_ALIAS',
                            'message' => '请筛选广告商',
                        ]);
                    }

                    $builder->where($clickField,'>',0);

                    if($requestData['adv_alias'] == AdvAliasEnum::OCEAN){
                        !empty($requestData['unit_id']) && $builder->whereRaw("{$clickField} IN (SELECT id FROM n8_adv_ocean.clicks WHERE ad_id = {$requestData['unit_id']})");
                        !empty($requestData['convert_callback_status']) && $builder->whereRaw("{$convertId} IN (SELECT convert_id FROM n8_adv_ocean.convert_callbacks WHERE convert_type = '{$convertType}' AND convert_callback_status = '{$requestData['convert_callback_status']}')");
                    }elseif($requestData['adv_alias'] == AdvAliasEnum::BD){
                        !empty($requestData['unit_id']) && $builder->whereRaw("{$clickField} IN (SELECT id FROM n8_adv_bd.clicks WHERE adgroup_id = {$requestData['unit_id']})");
                        !empty($requestData['convert_callback_status']) && $builder->whereRaw("{$convertId} IN (SELECT convert_id FROM n8_adv_bd.convert_callbacks WHERE convert_type = '{$convertType}' AND convert_callback_status = '{$requestData['convert_callback_status']}')");
                    }elseif($requestData['adv_alias'] == AdvAliasEnum::KS){
                        !empty($requestData['unit_id']) && $builder->whereRaw("{$clickField} IN (SELECT id FROM n8_adv_ks.clicks WHERE unit_id = {$requestData['unit_id']})");
                        !empty($requestData['convert_callback_status']) && $builder->whereRaw("{$convertId} IN (SELECT convert_id FROM n8_adv_ks.convert_callbacks WHERE convert_type = '{$convertType}' AND convert_callback_status = '{$requestData['convert_callback_status']}')");
                    }elseif($requestData['adv_alias'] == AdvAliasEnum::UC){
                        !empty($requestData['unit_id']) && $builder->whereRaw("{$clickField} IN (SELECT id FROM n8_adv_uc.clicks WHERE campaign_id = {$requestData['unit_id']})");
                        !empty($requestData['convert_callback_status']) && $builder->whereRaw("{$convertId} IN (SELECT convert_id FROM n8_adv_uc.convert_callbacks WHERE convert_type = '{$convertType}' AND convert_callback_status = '{$requestData['convert_callback_status']}')");
                    }
                }
            });
        });
    }

    // open id 筛选
    public function selectFilterOpenId(){
        $this->curdService->selectQueryBefore(function(){
            $this->curdService->customBuilder(function ($builder){
                $openId = $this->curdService->requestData['open_id'] ?? '';
                if(!empty($openId)){
                    $globalUser = (new N8GlobalUserData())
                        ->setParams([
                            'product_id' => $this->curdService->requestData['product_id'],
                            'open_id'  => $openId
                        ])
                        ->read();
                    if(!empty($globalUser)) $globalUser['n8_guid'] = 0;

                    $builder->where('n8_guid',$globalUser['n8_guid']);
                }
            });
        });
    }


    // 查询后映射转化信息
    public function selectConvertMap($convertType,$convertId = 'id'){
        $this->curdService->selectQueryAfter(function() use ($convertType,$convertId){
            $responseData = $this->curdService->responseData;

            if(!empty($responseData['list'])){
                //转化回传
                $convertList = (new ConvertCallbackMapService())
                    ->listMap($responseData['list'],$convertType,$convertId);

                foreach ($this->curdService->responseData['list'] as $item){

                    $convertKey = $item[$convertId];
                    $convertCallback = [];
                    if(isset($convertList[$convertKey])){
                        $convertCallback = $convertList[$convertKey]['convert_callback'] ?? [];
                    }

                    $item->convert_callback = $item->convert_callback ?? [];
                    $item->convert_callback += $this->itemConvertCallBack($convertType,$convertCallback);
                }
            }

        });
    }


    public function itemConvertCallBack($convertType,$convertCallback){
        return $convertCallback;
    }

    // 查询后映射自定义转化信息
    public function selectCustomConvertMap($convertType,$convertId = 'id'){
        $this->curdService->selectQueryAfter(function() use ($convertType,$convertId){
            $responseData = $this->curdService->responseData;

            if(!empty($responseData['list'])){

                //自定义转化回传
                $customConvertList = (new CustomConvertCallbackMapService())
                    ->listMap($responseData['list'],$convertType,$convertId);

                foreach ($this->curdService->responseData['list'] as $item){

                    $convertKey = $item[$convertId];

                    $item->custom_convert_callbacks = $customConvertCallbackMap = [];
                    if(isset($customConvertList[$convertKey])){
                        $tmp = $customConvertList[$convertKey]['custom_convert_callbacks'] ?? [];
                        $item->custom_convert_callbacks = $tmp;
                        $customConvertCallbackMap = array_column($tmp,null,'custom_convert_type');
                    }


                    //是否可自定义回传 （已自定义回传过）
                    $item->has_custom_convert_callback = [
                        $convertType => !isset($customConvertCallbackMap[$convertType])
                    ];
                }
            }

        });
    }
}
