<?php


namespace App\Http\Controllers\Admin;


use App\Common\Helpers\Functions;
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

    public $adminUser;


    /**
     * @var bool
     * convert_callback 是否根据转化类型做为下标
     */
    public $isConvertCallbackKey = false;

    public function __construct(){
        parent::__construct();
        $this->adminUser = Functions::getGlobalData('admin_user_info');
        $this->adminMap = $this->getAdminUserMap();

    }


    /**
     * 有数据权限
     * @return bool
     */
    public function isDataAuth(){
        if($this->adminUser['is_admin']) return true;

        return false;
    }


    public function selectCommonFilter(){


        $this->curdService->selectQueryBefore(function(){
            $this->curdService->customBuilder(function ($builder){

                $unionWhere = '1';
                if(!$this->isDataAuth()){
                    $unionWhere .= ' AND admin_id = ' . $this->adminUser['admin_user']['id'];
                }

                $createdTime = $this->curdService->requestData['created_time'] ?? [];

                if(!empty($createdTime)){
                    $unionWhere .= " AND created_time BETWEEN '{$createdTime[0]}' AND '{$createdTime[1]}'";
                }
                if($unionWhere != '1'){
                    $builder->whereRaw("uuid IN (SELECT id FROM n8_union_users WHERE {$unionWhere})");
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
