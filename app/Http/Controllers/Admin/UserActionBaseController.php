<?php


namespace App\Http\Controllers\Admin;


use App\Common\Enums\AdvAliasEnum;
use App\Common\Enums\PlatformEnum;
use App\Common\Helpers\Functions;
use App\Common\Tools\CustomException;
use App\Services\ConvertCallbackMapService;

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

                $requestData = $this->curdService->requestData;
                $unionWhere = '1';
                if(!$this->isDataAuth()){
                    $unionWhere .= ' AND admin_id = ' . $this->adminUser['admin_user']['id'];
                }

                $adminId = $requestData['admin_id'] ?? 0;
                if(!empty($adminId)){
                    $unionWhere .= ' AND admin_id = ' . $adminId;
                }

                $platform = $requestData['platform'] ?? '';
                if(!empty($platform)){
                    Functions::hasEnum(PlatformEnum::class,$platform);
                    $unionWhere .= " AND platform = '{$platform}'";
                }


                $createdTime = $requestData['created_time'] ?? [];

                if(!empty($createdTime)){
                    $unionWhere .= " AND created_time BETWEEN '{$createdTime[0]}' AND '{$createdTime[1]}'";
                }
                if($unionWhere != '1'){
                    $builder->whereRaw("uuid IN (SELECT id FROM n8_union_users WHERE {$unionWhere})");
                }


                // 广告单元id 回传状态筛选
                if(!empty($requestData['unit_id']) ||  !empty($requestData['convert_callback_status'])){

                    if(empty($requestData['adv_alias'])){
                        throw new CustomException([
                            'code' => 'NOT_ADV_ALIAS',
                            'message' => '请筛选广告商',
                        ]);
                    }

                    if($requestData['adv_alias'] == AdvAliasEnum::OCEAN){
                        !empty($requestData['unit_id']) && $builder->whereRaw("{$this->clickField} IN (SELECT id FROM n8_adv_ocean.clicks WHERE ad_id = {$requestData['unit_id']})");
                        !empty($requestData['convert_callback_status']) && $builder->whereRaw("{$this->convertId} IN (SELECT convert_id FROM n8_adv_ocean.convert_callbacks WHERE convert_type = '{$this->convertType}' AND convert_callback_status = '{$requestData['convert_callback_status']}')");
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
                $convertList = (new ConvertCallbackMapService())
                    ->listMap($this->curdService->responseData['list'],$convertType,$this->convertId);

                foreach ($this->curdService->responseData['list'] as $item){
                    if(isset($convertList[$item[$this->convertId]])){
                        $convertCallback = $convertList[$item[$this->convertId]]['convert_callback'] ?? [];

                    }else{
                        $convertCallback = [];
                    }

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

            }
        });
    }

}
