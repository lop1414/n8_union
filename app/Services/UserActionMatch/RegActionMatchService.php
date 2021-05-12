<?php

namespace App\Services\UserActionMatch;


use App\Common\Enums\ConvertTypeEnum;
use App\Common\Enums\MatcherEnum;
use App\Common\Services\SystemApi\AdvOceanApiService;
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



    public function getQuery(){
        $before = $this->getMatchCycleTime();

        return $this->model
            ->where('adv_alias',$this->advAlias)
            ->when($this->timeRange,function ($query){
                $query->whereBetween('created_time',$this->timeRange);
            })
            ->where('click_id',0)
            ->where('channel_Id','>',0)
            ->whereRaw(" (last_match_time IS NULL OR last_match_time <= '{$before}')")
            ->orderBy('created_time');
    }


    public function ocean(){

        $this->modelListPage(function ($list){
            $convert = [];
            foreach ($list as $item){

                $tmp = [
                    'convert_type' => $this->convertType,
                    'convert_id'   => $item['id'],
                    'convert_at'   => $item['created_time'],
                    'convert_times'=> 1,
                    'n8_union_user'=> [
                        'guid'  => $item['n8_guid'],
                        'channel_id' => $item['channel_id'],
                        'created_at' => $item['created_time'],
                        'click_source'  => $this->getAdvClickSourceEnum($item['matcher'])
                    ]
                ];

                $extend = $item->extend ? $item->extend->toArray() : [];

                // CP方归因 且 没有request id 不进行匹配
                $requestId = $extend['request_id'] ?? '';
                if($item['matcher'] == MatcherEnum::CP && empty($requestId)){
                    continue;
                }

                array_push($convert,array_merge($tmp,$extend));
            }

            if(!empty($convert)){
                $matchList = (new AdvOceanApiService())->apiConvertMatch($convert);

                // 保存click_id
                $lastMatchTime = date('Y-m-d H:i:s');
                foreach ($matchList as $match){
                    $updateData = [
                        'last_match_time'  => $lastMatchTime
                    ];
                    if($match['click_id'] > 0){
                        $updateData['click_id'] = $match['click_id'];
                    }

                    $where = ['id' => $match['convert_id']];
                    (new N8UnionUserData())->update($where,$updateData);
                }
            }


        });
    }



}
