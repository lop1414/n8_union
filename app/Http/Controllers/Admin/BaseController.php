<?php


namespace App\Http\Controllers\Admin;


use App\Common\Controllers\Admin\AdminController;
use App\Datas\N8GlobalUserData;


class BaseController extends AdminController
{


    /**
     * @param null $fn
     * 分页列表筛选 用户 公共处理
     */
    public function selectUserCommonFilter($fn = null){
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

                if(!empty($fn)){
                    $fn($builder);
                }
            });
        });
    }
}
