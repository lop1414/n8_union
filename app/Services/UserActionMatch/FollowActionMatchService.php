<?php

namespace App\Services\UserActionMatch;


use App\Common\Enums\ConvertTypeEnum;
use App\Common\Services\SystemApi\AdvOceanApiService;
use App\Datas\N8UnionUserData;
use App\Models\UserFollowActionModel;


class FollowActionMatchService extends UserActionMatchService
{

    protected $convertType = ConvertTypeEnum::FOLLOW;

    protected $unionUserData;

    public function __construct(){
        parent::__construct();

        $model = new UserFollowActionModel();
        $this->setModel($model);
        $this->unionUserData = new N8UnionUserData();
    }



    public function getQuery(){
        return $this->model
            ->where('adv_alias',$this->advAlias)
            ->when($this->timeRange,function ($query){
                $query->whereBetween('created_at',$this->timeRange);
            })
            ->where('click_id',0)
            ->orderBy('action_time');
    }


    public function ocean(){

        $this->modelListPage(function ($list){

            $convert = [];
            foreach ($list as $item){

                $unionUser = $this->unionUserData->setParams([
                    'n8_guid'   => $item['n8_guid'],
                    'channel_id'=> $item['channel_id']
                ])->read();

                $tmp = [
                    'convert_type' => $this->convertType,
                    'convert_id'   => $item['id'],
                    'convert_at'   => $item['action_time'],
                    'convert_times'=> 1,
                    'click_id'     => $unionUser['click_id'],
                    'n8_union_user'=> [
                        'guid'          => $unionUser['n8_guid'],
                        'channel_id'    => $unionUser['channel_id'],
                        'created_at'    => $unionUser['created_time'],
                        'click_source'  => $this->getAdvClickSourceEnum($unionUser['matcher'])
                    ]
                ];

                array_push($convert,array_merge($tmp,$this->getDeviceInfo($item)));
            }


            if(!empty($convert)) {
                $matchList = (new AdvOceanApiService())->apiConvertMatch($convert);

                // 保存click_id
                foreach ($matchList as $match){
                    if($match['click_id'] > 0){
                        (new UserFollowActionModel)
                            ->where('id',$match['convert_id'])
                            ->update(['click_id' => $match['click_id']]);
                    }
                }
            }
        });
    }



}
