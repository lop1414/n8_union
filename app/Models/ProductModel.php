<?php

namespace App\Models;

use App\Common\Enums\StatusEnum;
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
     * @param $value
     * @return array
     * 属性访问器
     */
    public function getExtendsAttribute($value)
    {
        return json_decode($value,true);
    }

    /**
     * @param $value
     * 属性修饰器
     */
    public function setExtendsAttribute($value)
    {
        $this->attributes['extends'] = json_encode($value);
    }


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

    public function money_divide(){
        return $this->hasOne('App\Models\ProductMoneyDivideModel', 'product_id', 'id');
    }

    public function product_admin(){
        return $this->hasMany('App\Models\ProductAdminModel','product_id','id')
            ->where('status',StatusEnum::ENABLE);

    }

}
