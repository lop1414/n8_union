<?php


namespace App\Datas;


use App\Common\Datas\BaseData;
use App\Models\ChannelModel;

class ChannelData extends BaseData
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
        ['product_id','n8_cp_channel_id']
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
        parent::__construct(ChannelModel::class);
    }


}
