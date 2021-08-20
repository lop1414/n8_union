<?php


namespace App\Datas;


use App\Common\Datas\BaseData;
use App\Common\Enums\AdvAliasEnum;
use App\Common\Enums\PlatformEnum;
use App\Common\Tools\CustomException;
use App\Models\N8UnionUserExtendModel;
use App\Models\N8UnionUserModel;
use App\Models\UserExtendModel;
use Jenssegers\Agent\Agent;

class N8UnionUserData extends BaseData
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
    protected $uniqueKeys = [
        ['n8_guid','channel_id']
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
        parent::__construct(N8UnionUserModel::class);
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
