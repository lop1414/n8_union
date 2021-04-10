<?php


namespace App\Http\Controllers\Admin;


use App\Datas\N8GlobalUserData;
use App\Models\UserModel;

class UserController extends BaseController
{

    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new UserModel();

        parent::__construct();
    }



    /**
     * 分页列表预处理
     */
    public function selectPrepare(){
        $this->curdService->addField('product_id')->addValidRule('required');

        $this->curdService->selectQueryBefore(function(){
            $this->curdService->customBuilder(function ($builder){
                $builder->where('product_id',$this->curdService->requestData['product_id']);

                $openId = $this->curdService->requestData['open_id'] ?? '';

                if(!empty($openId)){
                    $globalUser = (new N8GlobalUserData())
                        ->setParams([
                            'product_id' => $this->curdService->requestData['product_id'],
                            'open_id'   => $openId
                        ])
                        ->read();
                    if(!empty($globalUser)){
                        $builder->where('n8_guid',$globalUser['n8_guid']);
                    }
                }
            });
        });

        $this->curdService->selectQueryAfter(function(){
            foreach ($this->curdService->responseData['list'] as $item){
                $item->channel;
                $item->extend;
            }
        });
    }





    public function readPrepare(){
        $this->curdService->findAfter(function(){
            $this->curdService->responseData->channel;
            $this->curdService->responseData->extend;
        });
    }


}
