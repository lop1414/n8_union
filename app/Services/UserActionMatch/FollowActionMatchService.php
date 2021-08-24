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



    public function getQuery(){
        $before = $this->getMatchCycleTime();

        return $this->model
            ->select(DB::raw("user_follow_actions.*"))
            ->leftJoin('n8_union_users AS u','user_follow_actions.uuid','=','u.id')
            ->where('u.adv_alias',$this->advAlias)
            ->when($this->timeRange,function ($query){
                $query->whereBetween('user_follow_actions.action_time',$this->timeRange);
            })
            ->where('user_follow_actions.click_id',0)
            ->where('u.channel_id','>',0)
            ->whereRaw(" (user_follow_actions.last_match_time IS NULL OR user_follow_actions.last_match_time <= '{$before}')")
            ->orderBy('user_follow_actions.action_time');
    }





    public function updateActionData($match){

        $updateData = [
            'last_match_time'  => date('Y-m-d H:i:s')
        ];

        if($match['click_id'] > 0){
            $updateData['click_id'] = $match['click_id'];
        }

        $where = ['id'=> $match['convert_id']];
        (new UserFollowActionData())->update($where,$updateData);
    }

}
