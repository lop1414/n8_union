<?php

namespace App\Services\UserActionMatch;


use App\Common\Enums\ConvertTypeEnum;
use App\Common\Enums\OrderStatusEnums;
use App\Common\Services\SystemApi\AdvOceanApiService;
use App\Datas\N8UnionUserData;
use App\Datas\OrderData;
use App\Models\OrderModel;


class CompleteOrderActionMatchService extends UserActionMatchService
{

    protected $convertType = ConvertTypeEnum::PAY;

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
            ->where('status',OrderStatusEnums::COMPLETE)
            ->when($this->timeRange,function ($query){
                $query->whereBetween('complete_time',$this->timeRange);
            })
            ->where('complete_click_id',0)
            ->where('channel_id','>',0)
            ->whereRaw(" (complete_last_match_time IS NULL OR complete_last_match_time <= '{$before}')")
            ->orderBy('complete_time');
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
                    'convert_times'=> $item['complete_times'],
                    'click_id'     => $unionUser['click_id'],
                    'amount'       => $item['amount'],
                    'order_type'   => $item['type'],
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
                        'complete_last_match_time'  => $lastMatchTime
                    ];

                    if($match['click_id'] > 0){
                        $updateData['complete_click_id'] = $match['click_id'];
                    }

                    $where = ['n8_goid'=> $match['convert_id']];
                    (new OrderData())->update($where,$updateData);
                }
            }


        });
    }



}
