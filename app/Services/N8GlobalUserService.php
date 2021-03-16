<?php

namespace App\Services;

use App\Common\Services\BaseService;
use App\Common\Tools\CustomException;
use App\Models\N8GlobalUserModel;
use App\Services\TableCache\N8GlobalUserTableCacheService;

class N8GlobalUserService extends BaseService
{

    protected $tableCacheService;

    public function __construct(){
        parent::__construct();
        $model = new N8GlobalUserModel();
        $this->setModel($model);
        $this->tableCacheService = new N8GlobalUserTableCacheService();
    }


    /**
     * @param $productId
     * @param $openId
     * @return mixed
     * @throws CustomException
     */
    public function make($productId,$openId){
        $info = $this->tableCacheService->getByOpenId($productId,$openId);
        if(!empty($info)){
            throw new CustomException([
                'code'    => 'GUID_EXIST',
                'message' => '用户已存在',
                'log'     => true,
                'data'    => $info
            ]);
        }

        $tmpInfo = $this->model->create([
            'open_id' => $openId,
            'product_id' => $productId,
        ]);

        $info = $tmpInfo->toArray();

        // 设置缓存
        $this->tableCacheService->setAllTypeCache($info);
        return $info;
    }


    public function read($guid){
        $service = new N8GlobalUserTableCacheService();

        return $service->get($guid);
    }



    public function readByOpenId($productId,$openId){
        $service = new N8GlobalUserTableCacheService();

        return $service->getByOpenId($productId,$openId);
    }



    public function del($guid){
        $info = $this->read($guid);
        if(empty($info)) return null;

        $service = new N8GlobalUserTableCacheService();
        $service->del($guid);
        $service->delByOpenId($info['product_id'],$info['open_id']);

        // 删表数据
        return (new N8GlobalUserModel())->where('n8_guid',$guid)->delete();

    }


    public function delByOpenId($productId,$openId){
        $info = $this->readByOpenId($productId,$openId);
        if(empty($info)) return null;

        $service = new N8GlobalUserTableCacheService();

        $service->delByOpenId($productId,$openId);
        $service->del($info['n8_guid']);

        // 删表数据
        return (new N8GlobalUserModel())
            ->where('product_id',$productId)
            ->where('open_id',$openId)
            ->delete();

    }

}
