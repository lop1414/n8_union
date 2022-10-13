<?php

namespace App\Services\UserActionMatch;


use App\Common\Enums\ConvertTypeEnum;
use App\Common\Enums\MatcherEnum;
use App\Datas\N8UnionUserData;
use App\Models\N8UnionUserModel;


class RegActionMatchService extends UserActionMatchService
{

    protected $convertType = ConvertTypeEnum::REGISTER;


    public function __construct(){
        parent::__construct();
        $model = new N8UnionUserModel();
        $this->setModel($model);
    }



    public function getQuery($param = []){
        $before = $this->getMatchCycleTime();

        $query =  $this->model
            ->where('adv_alias',$this->advAlias)
            ->where('channel_id','>',0)
            ->whereRaw(" (last_match_time IS NULL OR last_match_time <= '{$before}')")
            ->orderBy('created_time');

        if(isset($param['n8_guid'])){
            return $query->where('n8_guid',$param['n8_guid']);
        }


        return $query
            ->where('click_id',0)
            ->when($this->timeRange,function ($query){
                return $query->whereBetween('created_time',$this->timeRange);
            });
    }



    public function isCanMatch($item,$unionUser){
        $extend = $item->extend ? $item->extend->toArray() : [];
        $requestId = $extend['request_id'] ?? '';

        if( $item['matcher'] != MatcherEnum::SYS && empty($requestId)){
            // request id 兼容二版上报过来数据 后面可以去掉
            echo "归因方不是系统 且没有request id不进行匹配( n8_guid:{$unionUser['n8_guid']} )\n";
            return false;
        }

        return true;
    }


    public function getConvertMatchData($item,$unionUser){
        return [
            'convert_type' => $this->convertType,
            'convert_id'   => $item['id'],
            'convert_at'   => $item['created_time'],
            'convert_times'=> 1,
            'n8_union_user'=> $this->filterUnionUser($item,$unionUser)
        ];
    }


    public function updateActionData($match){
        if($match['click_id'] <= 0){
            return ;
        }

        $where = ['id' => $match['convert_id']];
        (new N8UnionUserData())->update($where,['click_id' => $match['click_id']]);
    }




}
