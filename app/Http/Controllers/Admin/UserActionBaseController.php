<?php


namespace App\Http\Controllers\Admin;


use App\Services\ConvertCallbackMapService;

class UserActionBaseController extends BaseController
{

    public $mapUnionUser = true;

    public $convertId = 'id';

    public $convertType ;

    /**
     * @var bool
     * convert_callback 是否根据转化类型做为下标
     */
    public $isConvertCallbackKey = false;



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
                    $convertCallback = $convertList[$item[$this->convertId]]['convert_callback'];
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
                        $item->union_user = $this->model->union_user($item->n8_guid,$item->channel_id);
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

            $convertList = (new ConvertCallbackMapService())
                ->listMap([$this->curdService->responseData],$convertType,$this->convertId);

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
            if($this->mapUnionUser){
                $this->curdService->responseData->union_user = $this->model->union_user($this->curdService->responseData->n8_guid,$this->curdService->responseData->channel_id);
            }
        });
    }

}
