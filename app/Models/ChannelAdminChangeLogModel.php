<?php

namespace App\Models;

use App\Common\Models\BaseModel;

class ChannelAdminChangeLogModel extends BaseModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'channel_admin_change_logs';



    protected $fillable = [
        'channel_id',
        'admin_id_after',
        'admin_id_before'
    ];



    public function channel(){
        return $this->hasOne('App\Models\ChannelModel', 'id', 'channel_id');
    }


}
