<?php


namespace App\Datas;


use App\Common\Datas\BaseData;
use App\Models\N8GlobalOrderModel;

class N8GlobalOrderData extends BaseData
{


    /**
     * @var array
     * 字段
     */
    protected $fields = [];


    /**
     * @var array
     * 唯一键数组
     */
    protected $uniqueKeys = [
        ['order_id','product_id']
    ];


    /**
     * @var int
     * 缓存有效期
     */
    protected $ttl = 60*60*24;


    /**
     * constructor.
     */
    public function __construct(){
        parent::__construct(N8GlobalOrderModel::class);
    }


    /**
     * @param $productId
     * @param $orderId
     * @return mixed
     * 创建
     */
    public function create($productId,$orderId){
        $info = $this->getModel()->create([
            'product_id'    => $productId,
            'order_id'       => $orderId
        ]);
        return $info->toArray();
    }

}
