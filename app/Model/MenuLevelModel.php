<?php

namespace App\Models;

use App\Common\Models\BaseModel;

class MenuLevelModel extends BaseModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'menu_level';

    /**
     * 关联到模型数据表的主键
     *
     * @var string
     */
    protected $primaryKey = 'menu_id';

    /**
     * @var bool
     * 是否自增
     */
    public $incrementing = false;

}
