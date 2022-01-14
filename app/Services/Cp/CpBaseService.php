<?php

namespace App\Services\Cp;


use App\Common\Enums\StatusEnum;
use App\Common\Services\BaseService;
use App\Common\Tools\CustomException;
use App\Common\Traits\Hook;
use App\Models\ProductModel;

class CpBaseService extends BaseService
{
    use Hook;

    protected $cpType;
    protected $product;
    protected $param;


    public function getCpType(){
        return $this->cpType;
    }



    public function setProduct($info){
        $this->product = $info;
        return $this;
    }



    public function checkProduct(){
        if(empty($this->product)){
            throw new CustomException([
                'code' => 'NOT_SET_PRODUCT',
                'message' => '未设置产品信息',
                'log' => false
            ]);
        }
    }



    /**
     * @return mixed
     * 获取产品列表
     */
    public function getProductList(){
        $builder = $this->getProductBuilder();

        $productId = $this->getParam('product_id');
        if($productId){
            $builder->where('id',$productId);
        }

        $productType = $this->getParam('product_type');
        if($productType){
            $builder->where('type',$productType);
        }
        return $builder->get();
    }




    /**
     * @return mixed
     * 获取产品构造器
     */
    public function getProductBuilder(){
        $productModel = new ProductModel();
        return $productModel->where('cp_type',$this->cpType)->where('status',StatusEnum::ENABLE);
    }




    public function getParam($key){
        if(empty($this->param[$key])){
            return null;
        }
        return $this->param[$key];
    }



    public function setParam($key,$data){
        $this->param[$key] = $data;
    }



    public function syncWithHook(){

        $this->syncPrepare();

        $this->callHook('sync_before');

        //执行
        $ret = $this->sync();

        $this->callHook('sync_after');

        return $ret;
    }



    public function syncPrepare(){}



    /**
     * @param $func
     * 同步数据前钩子
     */
    public function syncBefore($func){
        $this->setHook('sync_before', $func);
    }



    /**
     * @param $func
     * 同步数据后钩子
     */
    public function syncAfter($func){
        $this->setHook('sync_after', $func);
    }


}
