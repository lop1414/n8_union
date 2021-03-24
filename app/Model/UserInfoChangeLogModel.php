<?php

namespace App\Models;

use App\Common\Models\BaseModel;

class UserInfoChangeLogModel extends BaseModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'user_info_change_logs';

    /**
     * 关联到模型数据表的主键
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * 禁用默认更新时间
     *
     * @var bool
     */
    public $timestamps = false;


    /**
     * @var array
     * 批量更新忽略字段
     */
    protected $updateIgnoreFields = [
        'created_at'
    ];



}
