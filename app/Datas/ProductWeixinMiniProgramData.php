<?php


namespace App\Datas;


use App\Common\Datas\BaseData;
use App\Models\ProductWeixinMiniProgramModel;

class ProductWeixinMiniProgramData extends BaseData
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
        ['product_id']
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
        parent::__construct(ProductWeixinMiniProgramModel::class);
    }



    public function save($data){
        $where = [
            'product_id' => $data['product_id'],
        ];

        //清除缓存
        $this->setParams($where)->clear();

        $info = $this->getModel()->where($where)->first();

        if(empty($info)){
            $info = $this->getModel();
            $info->product_id = $data['product_id'];
        }

        $info->weixin_mini_program_id = $data['weixin_mini_program_id'];
        $info->url = $data['url'];
        $info->path = $data['path'];
        $info->save();
        return $info;
    }

}
