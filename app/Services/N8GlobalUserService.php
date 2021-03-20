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



    public function readByOpenId($productId,$openId){
        $info = $this->tableCacheService->getByOpenId($productId,$openId);

        if(empty($info)){
            $tmpInfo = $this->model->create([
                'open_id' => $openId,
                'product_id' => $productId,
            ]);

            $info = $tmpInfo->toArray();
            // 设置缓存
            $this->tableCacheService->setAllTypeCache($info);
        }

        return $info;
    }




    public function read($guid){
        $service = new N8GlobalUserTableCacheService();

        return $service->get($guid);
    }


}
