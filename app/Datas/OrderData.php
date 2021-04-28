<?php


namespace App\Datas;


use App\Common\Datas\BaseData;
use App\Models\OrderModel;


class OrderData extends BaseData
{

    /**
     * @var bool
     * 缓存开关
     */
    protected $cacheSwitch = true;


    /**
     * @var array
     * 字段
     */
    protected $fields = [];


    /**
     * @var array
     * 唯一键数组
     */
    protected $uniqueKeys = [];


    /**
     * @var int
     * 缓存有效期
     */
    protected $ttl = 60*60*24*3;


    /**
     * constructor.
     */
    public function __construct(){
        parent::__construct(OrderModel::class);
    }

}
