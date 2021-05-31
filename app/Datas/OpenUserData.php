<?php

namespace App\Datas;

use App\Common\Datas\BaseData;
use App\Models\OpenUserModel;

class OpenUserData extends BaseData
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
        ['user_source', 'source_app_id', 'source_open_id'],
    ];

    /**
     * @var int
     * 缓存有效期
     */
    protected $ttl = 60*60*24*7;

    /**
     * constructor.
     */
    public function __construct(){
        parent::__construct(OpenUserModel::class);
    }

    /**
     * @param $userSource
     * @param $sourceAppId
     * @param $sourceOpenId
     * @param $n8Guid
     * @return mixed
     * 创建
     */
    public function create($userSource, $sourceAppId, $sourceOpenId, $n8Guid){
        $model = $this->getModel();
        $model->user_source = $userSource;
        $model->source_app_id = $sourceAppId;
        $model->source_open_id = $sourceOpenId;
        $model->n8_guid = $n8Guid;
        $model->extends = [];
        return $model->save();
    }
}
