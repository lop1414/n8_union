<?php

namespace App\Services\UserActionMatch;


use App\Common\Enums\ConvertTypeEnum;
use App\Datas\N8UnionUserData;
use App\Datas\UserFollowActionData;
use App\Models\UserFollowActionModel;


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
            ->where('adv_alias',$this->advAlias)
            ->when($this->timeRange,function ($query){
                $query->whereBetween('action_time',$this->timeRange);
            })
            ->where('click_id',0)
            ->where('channel_id','>',0)
            ->whereRaw(" (last_match_time IS NULL OR last_match_time <= '{$before}')")
            ->orderBy('action_time');
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
