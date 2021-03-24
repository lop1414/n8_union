<?php


namespace App\Datas;


use App\Common\Datas\BaseData;
use App\Models\N8GlobalUserModel;

class N8GlobalUserData extends BaseData
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
        ['open_id','product_id']
    ];


    /**
     * @var int
     * 缓存有效期
     */
    protected $ttl = 60*60*24*3;


    /**
     * constructor.
     */
    public function __construct(){
        parent::__construct(N8GlobalUserModel::class);
    }



    /**
     * @param $productId
     * @param $openId
     * @return mixed
     * 创建
     */
    public function create($productId,$openId){
        $info = $this->getModel()->create([
            'product_id'    => $productId,
            'open_id'       => $openId
        ]);
        return $info->toArray();
    }

}
