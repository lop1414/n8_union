<?php

namespace App\Models\Qywx;

use App\Common\Models\BaseModel;

class QywxKefuModel extends BaseModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'qywx_kefus';

    /**
     * 关联到模型数据表的主键
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var string
     * 主键数据类型
     */
    public $keyType = 'string';

    /**
     * @var bool
     * 是否自增
     */
    public $incrementing = false;
}
