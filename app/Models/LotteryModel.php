<?php

namespace App\Models;

use App\Common\Models\BaseModel;

class LotteryModel extends BaseModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'lotterys';

    /**
     * @param $value
     * @return array
     * 属性访问器
     */
    public function getExtendsAttribute($value)
    {
        return json_decode($value);
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
     * @param $value
     * @return array
     * 属性访问器
     */
    public function getReleaseDataAttribute($value)
    {
        return json_decode($value, true);
    }

    /**
     * @param $value
     * 属性修饰器
     */
    public function setReleaseDataAttribute($value)
    {
        $this->attributes['release_data'] = json_encode($value);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * 关联抽奖奖品模型
     */
    public function lottery_prizes(){
        return $this->hasMany('App\Models\LotteryPrizeModel', 'lottery_id', 'id')->enable();
    }
}
