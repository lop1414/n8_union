<?php


namespace App\Datas;


use App\Common\Datas\BaseData;
use App\Models\CpChannelModel;

class CpChannelData extends BaseData
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
        ['product_id','cp_channel_id']
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
        parent::__construct(CpChannelModel::class);
    }


    public function save($data){
        return $this->model->updateOrCreate(
            [
                'product_id'    => $data['product_id'],
                'cp_channel_id' => $data['cp_channel_id']
            ],
            [
                'name'           => $data['name'],
                'book_id'        => $data['book_id'],
                'chapter_id'     => $data['chapter_id'],
                'force_chapter_id'   => $data['force_chapter_id'],
                'create_time'    => $data['create_time'],
                'updated_time'   => $data['updated_time'],
            ]
        );
    }

}
