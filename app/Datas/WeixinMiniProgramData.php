<?php


namespace App\Datas;


use App\Common\Datas\BaseData;
use App\Models\WeixinMiniProgramModel;

class WeixinMiniProgramData extends BaseData
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
        ['app_id']
    ];


    /**
     * @var int
     * 缓存有效期
     */
    protected $ttl = 60*60*2;


    /**
     * constructor.
     */
    public function __construct(){
        parent::__construct(WeixinMiniProgramModel::class);
    }



    public function save($data){
        $where = ['app_id' => $data['app_id'],];

        //清除缓存
        $this->setParams($where)->clear();

        $info = $this->getModel()->where($where)->first();

        if(empty($info)){
            $info = $this->getModel();
            $info->app_id = $data['app_id'];
        }

        if(isset($data['app_secret'])){
            $info->app_secret = $data['app_secret'];
        }

        $info->name = $data['name'];
        $info->access_token = $data['access_token'];
        $info->expired_at = $data['expired_at'];
        $info->save();
        return $info;
    }

}
