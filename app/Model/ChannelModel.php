<?php

namespace App\Models;

use App\Common\Models\BaseModel;

class ChannelModel extends BaseModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'channels';



    /**
     * CP渠道信息 一对一
     */
    public function cp_channel(){
        return $this->hasOne('App\Models\CpChannelModel', 'id', 'gcid');
    }


    /**
     * 产品
     */
    public function product(){
        return $this->hasOne('App\Models\ProductModel', 'id', 'product_id');
    }




}
