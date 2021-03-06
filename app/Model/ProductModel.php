<?php

namespace App\Models;

use App\Common\Models\BaseModel;

class ProductModel extends BaseModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'products';

    /**
     * 关联到模型数据表的主键
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * 隐藏字段
     *
     * @var array
     */
    protected $hidden = [
        'logo'
    ];


    /**
     * 关联全局用户 一对多
     */
    public function n8_global_user(){
        return $this->hasMany('App\Models\N8GlobalUserModel','product_id','id');
    }

    /**
     * 关联全局订单 一对多
     */
    public function n8_global_order(){
        return $this->hasMany('App\Models\N8GlobalOrderModel','product_id','id');
    }

    /**
     * 平台账户 一对一
     */
    public function cp_account(){
        return $this->hasOne('App\Models\CpAccountModel', 'id', 'cp_account_id');
    }

}
