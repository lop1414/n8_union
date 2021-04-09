<?php

namespace App\Services\UserActionMatch;


use App\Common\Enums\ConvertTypeEnum;
use App\Common\Enums\OrderStatusEnums;
use App\Common\Services\SystemApi\AdvOceanApiService;
use App\Datas\N8UnionUserData;
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
        return $this->model
            ->where('adv_alias',$this->advAlias)
            ->where('status',OrderStatusEnums::COMPLETE)
            ->when($this->timeRange,function ($query){
                $query->whereBetween('created_at',$this->timeRange);
            })
            ->where('complete_click_id',0)
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

                $convertTimes = (new OrderModel())
                    ->where('n8_guid',$item['n8_guid'])
                    ->where('channel_id',$item['channel_id'])
                    ->where('status',OrderStatusEnums::COMPLETE)
                    ->where('order_time','<',$item['order_time'])
                    ->count();

                $tmp = [
                    'convert_type' => $this->convertType,
                    'convert_id'   => $item['n8_goid'],
                    'convert_at'   => $item['order_time'],
                    'convert_times'=> $convertTimes + 1,
                    'n8_union_user'=> [
                        'guid'          => $unionUser['n8_guid'],
                        'channel_id'    => $unionUser['channel_id'],
                        'created_at'    => $unionUser['created_time']
                    ]
                ];

                $extend = $item->extend ? $item->extend->toArray() : [];
                array_push($convert,array_merge($tmp,$extend));
            }


            $matchList = (new AdvOceanApiService())->apiConvertMatch($convert);

            // 保存click_id
            foreach ($matchList as $match){
                if($match['click_id'] > 0){
                    (new OrderModel())
                        ->where('n8_goid',$match['convert_id'])
                        ->update(['complete_click_id' => $match['click_id']]);
                }
            }

        });
    }



}
