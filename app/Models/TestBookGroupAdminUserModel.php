<?php

namespace App\Models;

use App\Common\Models\BaseModel;

class TestBookGroupAdminUserModel extends BaseModel
{

    /**
     * 禁用默认更新时间
     *
     * @var bool
     */
    public $timestamps = false;
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'test_book_group_admin_users';
    protected $fillable = [
        'admin_id',
        'test_group_id'
    ];

}
