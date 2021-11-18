<?php


namespace App\Datas;


use App\Common\Datas\BaseData;
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

        $this->model->updateOrCreate($where, [
                'name'          => $data['title'],
                'author_name'   => $data['author_name'],
                'all_words'     => $data['all_words'],
                'update_time'   => $data['update_time']
            ]
        );
    }

}
