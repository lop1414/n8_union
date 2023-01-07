<?php


namespace App\Datas;


use App\Common\Datas\BaseData;
use App\Common\Enums\MatcherEnum;
use App\Common\Enums\OperatorEnum;
use App\Common\Enums\StatusEnum;
use App\Models\ProductModel;

class ProductData extends BaseData
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
        ['cp_product_alias','cp_type']
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
        parent::__construct(ProductModel::class);
    }



    public function save($data){
        $where = [
            'cp_product_alias' => $data['cp_product_alias'],
            'cp_type'          => $data['cp_type']
        ];

        //清除缓存
        $this->setParams($where)->clear();

        $info = (clone $this->getModel())->where($where)->first();

        if(empty($info)){
            $info = clone $this->getModel();
            $info->cp_account_id = $data['cp_account_id'];
            $info->cp_product_alias = $data['cp_product_alias'];
            $info->cp_type = $data['cp_type'];
            $info->type = $data['type'];
            $info->secret = md5(uniqid());
            $info->status = StatusEnum::DISABLE;
            $info->matcher = MatcherEnum::SYS;
            $info->operator = OperatorEnum::SYS;
        }
        if(isset($data['cp_secret'])){
            $info->cp_secret = $data['cp_secret'];
        }
        if(isset($data['extends'])){
            $info->extends = $data['extends'];
        }
        $info->name = $data['name'];
        $info->save();
        return $info;
    }

}
