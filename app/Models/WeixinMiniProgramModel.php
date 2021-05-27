<?php

namespace App\Models;

use App\Common\Models\BaseModel;

class WeixinMiniProgramModel extends BaseModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'weixin_mini_programs';

    /**
     * @var array
     * 隐藏字段
     */
    protected $hidden = ['app_secret'];
}
