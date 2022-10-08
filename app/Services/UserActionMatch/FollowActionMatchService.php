<?php

namespace App\Services\UserActionMatch;


use App\Common\Enums\ConvertTypeEnum;
use App\Datas\N8UnionUserData;
use App\Datas\UserFollowActionData;
use App\Models\UserFollowActionModel;
use Illuminate\Support\Facades\DB;


class FollowActionMatchService extends UserActionMatchService
{

    protected $convertType = ConvertTypeEnum::FOLLOW;


    public function __construct(){
        parent::__construct();

        $model = new UserFollowActionModel();
        $this->setModel($model);
    }



    public function getQuery($param = []){
        $before = $this->getMatchCycleTime();

        $query = $this->model
            ->select(DB::raw("user_follow_actions.*"))
            ->leftJoin('n8_union_users AS u','user_follow_actions.uuid','=','u.id')
            ->where('u.adv_alias',$this->advAlias)
            ->where('u.channel_id','>',0)
            ->whereRaw(" (user_follow_actions.last_match_time IS NULL OR user_follow_actions.last_match_time <= '{$before}')")
            ->orderBy('user_follow_actions.action_time');

        if(isset($param['n8_guid'])){
            return $query->where('n8_guid',$param['n8_guid']);
        }

        return $query
            ->where('u.click_id','>',0)
            ->where('user_follow_actions.click_id',0)
            ->when($this->timeRange,function ($query){
                $query->whereBetween('user_follow_actions.action_time',$this->timeRange);
            });
    }





    public function updateActionData($match){
        if($match['click_id'] <= 0){
            return ;
        }

        $where = ['id' => $match['convert_id']];
        (new UserFollowActionData())->update($where,['click_id' => $match['click_id']]);
    }

}
