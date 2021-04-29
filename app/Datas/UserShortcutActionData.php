<?php


namespace App\Datas;


use App\Common\Datas\BaseData;
use App\Models\UserShortcutActionModel;

class UserShortcutActionData extends BaseData
{

    /**
     * @var bool
     * 缓存开关
     */
    protected $cacheSwitch = false;

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
    protected $ttl = 60*60*24;


    /**
     * constructor.
     */
    public function __construct(){
        parent::__construct(UserShortcutActionModel::class);
    }



    public function update($where = [],$update = []){
        if(empty($update)) return;

        $this->model
            ->where($where)
            ->update($update);

        // 删除缓存
        $this->setParams($where)->clear();
    }



}
