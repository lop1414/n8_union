<?php

namespace App\Services\TableCache;

use App\Common\Services\BaseService;
use App\Common\Tools\CustomException;
use App\Common\Tools\TableCache;
use App\Models\N8GlobalUserModel;

class N8GlobalUserTableCacheService extends BaseService
{

    protected $tableCacheTool,$tableCacheToolByOpenId;

    public function __construct(){
        parent::__construct();
        $model = new N8GlobalUserModel();
        $this->setModel($model);
        $this->tableCacheTool = new TableCache($model,'n8_guid');
        $this->tableCacheToolByOpenId = new TableCache($model,'open_id');
    }


    public function get($guid){
        return $this->tableCacheTool->get($guid);

    }



    public function set($data){
        return $this->tableCacheTool->set($data['n8_guid'],$data);
    }


    public function del($guid){
        return $this->tableCacheTool->del($guid);
    }




    public function getKeyByOpenId($data){
        return  $data['product_id'].'-'.$data['open_id'];
    }


    public function getByOpenId($productId,$openId){
        $key = $this->getKeyByOpenId([
            'product_id' => $productId,
            'open_id'    => $openId
        ]);
        return $this->tableCacheToolByOpenId->get($key);
    }


    public function setByOpenId($data){
        $key = $this->getKeyByOpenId($data);
        return $this->tableCacheToolByOpenId->set($key,$data);
    }


    public function delByOpenId($productId,$openId){
        $key = $this->getKeyByOpenId([
            'product_id' => $productId,
            'open_id'    => $openId
        ]);
        return $this->tableCacheToolByOpenId->del($key);
    }



    /**
     * 设置所有类型缓存
     *
     * @param $data
     */
    public function setAllTypeCache($data){
        $this->set($data);
        $this->setByOpenId($data);
    }




    /**
     * 是否存在
     * @param $productId
     * @param $openId
     * @return bool
     * @throws CustomException
     */
    public function isExistByOpenId($productId,$openId){
        $guidInfo = $this->getByOpenId($productId,$openId);

        if(empty($guidInfo)){
            throw new CustomException([
                'code'    => 'NOT_GUID_BY_OPEN_ID',
                'message' => '找不到用户',
                'log'     => true,
                'data'    => [
                    'product_id' => $productId,
                    'open_id'    => $openId
                ]
            ]);
        }
        return $guidInfo;
    }




    public function refreshAllCache(){

        // 删除
        $tmpList = $this->tableCacheTool->keys();

        foreach ($tmpList as $cacheKey){
            $this->tableCacheTool->del($cacheKey);
        }

        unset($tmpList);

        $tmpList = $this->tableCacheToolByOpenId->keys();

        foreach ($tmpList as $cacheKey){
            $this->tableCacheTool->del($cacheKey);
        }
        unset($tmpList);

        // 创建
        $page = 1;
        do{
            $data = $this->getModel()->listPage($page,10000);
            $items = $data['list']->toArray();
            foreach ($items as $item){
                $this->setAllTypeCache($item);
            }

            $page +=1;
        }while($data['page_info']['total_page'] >= $page);

    }

}
