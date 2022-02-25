<?php


namespace App\Http\Controllers\Admin;


use App\Common\Enums\AdvAliasEnum;
use App\Common\Enums\ConvertTypeEnum;
use App\Common\Enums\PlatformEnum;
use App\Common\Helpers\Functions;
use App\Common\Tools\CustomException;
use App\Services\ConvertCallbackMapService;
use App\Services\CustomConvertCallbackMapService;

class UserActionBaseController extends BaseController
{

    /**
     * @var string
     * 默认排序字段
     */
    protected $defaultOrderBy = 'action_time';

    public $mapUnionUser = true;

    public $convertId = 'id';

    public $convertType ;

    protected $adminMap;

    /**
     * @var string
     * 点击id字段
     */
    public $clickField = 'click_id';



    /**
     * @var bool
     * convert_callback 是否根据转化类型做为下标
     */
    public $isConvertCallbackKey = false;

    public function __construct(){
        parent::__construct();
        $this->adminMap = $this->getAdminUserMap();

    }


    public function selectCommonFilter(){

        $this->curdService->selectQueryBefore(function(){
            $this->curdService->customBuilder(function ($builder){
                $adminId = $this->curdService->requestData['admin_id'] ?? 0;

                $requestData = $this->curdService->requestData;
                $unionWhere = '1';

                $isSelf = $requestData['is_self'] ?? 1;
                if($isSelf){
                    $adminId = $this->adminUser['admin_user']['id'];
                }elseif(!$this->isDataAuth()) {
                    $unionWhere .= ' AND admin_id IN (' . $this->getPermissionAdminIdsStr() .')';
                }

                if(!empty($adminId)){
                    $unionWhere .= ' AND admin_id = ' . $adminId;
                }

                $platform = $requestData['platform'] ?? '';
                if(!empty($platform)){
                    Functions::hasEnum(PlatformEnum::class,$platform);
                    $unionWhere .= " AND platform = '{$platform}'";
                }

                $channelId = $requestData['channel_id'] ?? '';
                if(!empty($channelId)){
                    $unionWhere .= " AND channel_id = '{$channelId}'";
                }

                $userType = $requestData['user_type'] ?? '';
                if(!empty($userType)){
                    $unionWhere .= " AND user_type = '{$userType}'";
                }


                $createdTime = $requestData['created_time'] ?? [];

                if(!empty($createdTime)){
                    $unionWhere .= " AND created_time BETWEEN '{$createdTime[0]}' AND '{$createdTime[1]}'";
                }
                if($unionWhere != '1'){
                    $builder->whereRaw("uuid IN (SELECT id FROM n8_union_users WHERE {$unionWhere})");
                }
            });
        });
        $this->filterAdv();

    }


    /**
     * 广告单元id 回传状态筛选
     */
    protected function filterAdv(){
        $this->curdService->selectQueryBefore(function(){
            $this->curdService->customBuilder(function ($builder){
                $requestData = $this->curdService->requestData;
                if(!empty($requestData['unit_id']) ||  !empty($requestData['convert_callback_status'])){

                    if(empty($requestData['adv_alias'])){
                        throw new CustomException([
                            'code' => 'NOT_ADV_ALIAS',
                            'message' => '请筛选广告商',
                        ]);
                    }

                    $builder->where($this->clickField,'>',0);

                    if($requestData['adv_alias'] == AdvAliasEnum::OCEAN){
                        !empty($requestData['unit_id']) && $builder->whereRaw("{$this->clickField} IN (SELECT id FROM n8_adv_ocean.clicks WHERE ad_id = {$requestData['unit_id']})");
                        !empty($requestData['convert_callback_status']) && $builder->whereRaw("{$this->convertId} IN (SELECT convert_id FROM n8_adv_ocean.convert_callbacks WHERE convert_type = '{$this->convertType}' AND convert_callback_status = '{$requestData['convert_callback_status']}')");
                    }elseif($requestData['adv_alias'] == AdvAliasEnum::BD){
                        !empty($requestData['unit_id']) && $builder->whereRaw("{$this->clickField} IN (SELECT id FROM n8_adv_bd.clicks WHERE adgroup_id = {$requestData['unit_id']})");
                        !empty($requestData['convert_callback_status']) && $builder->whereRaw("{$this->convertId} IN (SELECT convert_id FROM n8_adv_bd.convert_callbacks WHERE convert_type = '{$this->convertType}' AND convert_callback_status = '{$requestData['convert_callback_status']}')");
                    }elseif($requestData['adv_alias'] == AdvAliasEnum::KS){
                        !empty($requestData['unit_id']) && $builder->whereRaw("{$this->clickField} IN (SELECT id FROM n8_adv_ks.clicks WHERE unit_id = {$requestData['unit_id']})");
                        !empty($requestData['convert_callback_status']) && $builder->whereRaw("{$this->convertId} IN (SELECT convert_id FROM n8_adv_ks.convert_callbacks WHERE convert_type = '{$this->convertType}' AND convert_callback_status = '{$requestData['convert_callback_status']}')");
                    }elseif($requestData['adv_alias'] == AdvAliasEnum::UC){
                        !empty($requestData['unit_id']) && $builder->whereRaw("{$this->clickField} IN (SELECT id FROM n8_adv_uc.clicks WHERE campaign_id = {$requestData['unit_id']})");
                        !empty($requestData['convert_callback_status']) && $builder->whereRaw("{$this->convertId} IN (SELECT convert_id FROM n8_adv_uc.convert_callbacks WHERE convert_type = '{$this->convertType}' AND convert_callback_status = '{$requestData['convert_callback_status']}')");
                    }

                }
            });
        });

    }


    /**
     * @param null $convertType
     * 分页列表公共预处理
     */
    public function selectCommonPrepare($convertType = null){
        $this->curdService->selectQueryAfter(function() use ($convertType){

            $convertType = $convertType ? : $this->convertType;

            if(!empty($this->curdService->responseData['list'])){
                //转化回传
                $convertList = (new ConvertCallbackMapService())
                    ->listMap($this->curdService->responseData['list'],$convertType,$this->convertId);

                //自定义转化回传
                $customConvertList = (new CustomConvertCallbackMapService())
                    ->listMap($this->curdService->responseData['list'],$convertType,$this->convertId);

                foreach ($this->curdService->responseData['list'] as $item){

                    $convertCallback = [];
                    if(isset($convertList[$item[$this->convertId]])){
                        $convertCallback = $convertList[$item[$this->convertId]]['convert_callback'] ?? [];
                    }

                    $customConvertCallbackMap = [];
                    if(isset($customConvertList[$item[$this->convertId]])){
                        $tmp = $customConvertList[$item[$this->convertId]]['custom_convert_callbacks'] ?? [];
                        $customConvertCallbackMap = array_column($tmp,null,'convert_type');
                    }

                    $item->custom_convert_callbacks = $customConvertCallbackMap;
                    $item->has_custom_convert_callback = [
                        ConvertTypeEnum::PAY => isset($customConvertCallbackMap[ConvertTypeEnum::PAY])
                    ];

                    // 映射回传信息
                    if($this->isConvertCallbackKey){

                        if(empty($item->convert_callback)){
                            $item->convert_callback = [];
                        }

                        $key = strtolower($convertType);
                        $item->convert_callback += [
                            $key => $convertCallback
                        ];
                    }else{
                        $item->convert_callback = $convertCallback;
                    }

                    $item->user;
                    $item->global_user;
                    $item->channel;
                    if($this->mapUnionUser){
                        $item->union_user;
                        $item->union_user->channel;
                        $item->admin_name = $this->adminMap[$item->union_user->admin_id]['name'];
                    }
                    $this->itemPrepare($item);
                }
            }

        });
    }


    public function itemPrepare($item){}



    public function readCommonPrepare($convertType = null){
        $this->curdService->findAfter(function() use ($convertType){
            $convertType = $convertType ? : $this->convertType;
            $convertId = $this->convertId;

            $convertList = (new ConvertCallbackMapService())
                ->listMap([$this->curdService->responseData],$convertType,$convertId);

            $convertCallback = $convertList[$this->curdService->responseData->$convertId]['convert_callback'];

            // 映射回传信息
            if($this->isConvertCallbackKey){

                if(empty($this->curdService->responseData->convert_callback)){
                    $this->curdService->responseData->convert_callback = [];
                }

                $key = strtolower($convertType);
                $this->curdService->responseData->convert_callback += [
                    $key => $convertCallback
                ];
            }else{
                $this->curdService->responseData->convert_callback = $convertCallback;
            }


            $this->curdService->responseData->user;
            $this->curdService->responseData->global_user;
            $this->curdService->responseData->channel;

            if($this->mapUnionUser) {
                $this->curdService->responseData->union_user;
                $this->curdService->responseData->admin_name = $this->adminMap[$this->curdService->responseData->admin_id]['name'];
                $this->curdService->responseData->union_user->channel;
            }
        });
    }

}
