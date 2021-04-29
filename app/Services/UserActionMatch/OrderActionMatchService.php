<?php

namespace App\Services\UserActionMatch;


use App\Common\Enums\ConvertTypeEnum;
use App\Common\Services\SystemApi\AdvOceanApiService;
use App\Datas\N8UnionUserData;
use App\Datas\OrderData;
use App\Datas\UserFollowActionData;
use App\Models\OrderModel;


class OrderActionMatchService extends UserActionMatchService
{

    protected $convertType = ConvertTypeEnum::ORDER;

    protected $unionUserData;

    public function __construct(){
        parent::__construct();

        $model = new OrderModel();
        $this->setModel($model);

        $this->unionUserData = new N8UnionUserData();
    }



    public function getQuery(){
        $before = $this->getMatchCycleTime();

        return $this->model
            ->where('adv_alias',$this->advAlias)
            ->when($this->timeRange,function ($query){
                $query->whereBetween('order_time',$this->timeRange);
            })
            ->where('click_id',0)
            ->where('channel_Id','>',0)
            ->whereRaw(" (order_last_match_time IS NULL OR order_last_match_time <= '{$before}')")
            ->orderBy('order_time');
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
                    'convert_id'   => $item['n8_goid'],
                    'convert_at'   => $item['order_time'],
                    'convert_times'=> $item['order_times'],
                    'click_id'     => $unionUser['click_id'],
                    'n8_union_user'=> [
                        'guid'          => $unionUser['n8_guid'],
                        'channel_id'    => $unionUser['channel_id'],
                        'created_at'    => $unionUser['created_time'],
                        'click_source'  => $this->getAdvClickSourceEnum($unionUser['matcher'])
                    ]
                ];

                $extend = $item->extend ? $item->extend->toArray() : [];
                array_push($convert,array_merge($tmp,$extend));
            }

            if(!empty($convert)) {
                $matchList = (new AdvOceanApiService())->apiConvertMatch($convert);

                // 保存click_id
                $lastMatchTime = date('Y-m-d H:i:s');
                foreach ($matchList as $match){

                    $updateData = [
                        'order_last_match_time'  => $lastMatchTime
                    ];

                    if($match['click_id'] > 0){
                        $updateData['click_id'] = $match['click_id'];
                    }

                    $where = ['n8_goid'=> $match['convert_id']];
                    (new OrderData())->update($where,$updateData);
                }
            }
        });
    }



}
