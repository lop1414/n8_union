<?php

namespace App\Services;


use App\Common\Services\BaseService;
use App\Common\Tools\CustomException;
use App\Datas\N8GlobalUserData;


class GlobalUserService extends BaseService
{

    protected $modelData;

    public function __construct(){
        parent::__construct();
        $this->modelData = new N8GlobalUserData();
    }


    /**
     * @param $productId
     * @param $openId
     * @return mixed|null
     * 制作全局用户信息
     */
    public function make($productId,$openId){

        $info = $this->read($productId, $openId);

        if(empty($info)){
            $info = $this->modelData->create($productId,$openId);
        }

        return $info;
    }



    public function read($productId,$openId){
        return $this->modelData
            ->setParams(['product_id' => $productId,'open_id' => $openId])
            ->read();
    }



    /**
     * @param $productId
     * @param $openId
     * @throws CustomException
     * 清除缓存
     */
    public function clearCache($productId,$openId){
        (new N8GlobalUserData())->setParams(['product_id' => $productId,'open_id' => $openId])->clear();
    }



}
