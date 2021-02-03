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
        $info = $this->tableCacheService->getInfoByOpenId($productId,$openId);
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


}
