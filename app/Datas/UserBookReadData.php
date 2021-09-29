<?php


namespace App\Datas;


use App\Common\Datas\BaseData;
use App\Models\UserBookReadModel;

class UserBookReadData extends BaseData
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
        ['n8_guid','book_id']
    ];


    /**
     * @var int
     * 缓存有效期
     */
    protected $ttl = 60*60*1;


    /**
     * constructor.
     */
    public function __construct(){
        parent::__construct(UserBookReadModel::class);
    }

}
