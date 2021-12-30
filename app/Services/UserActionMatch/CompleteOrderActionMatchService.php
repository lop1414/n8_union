<?php

namespace App\Services\UserActionMatch;


use App\Common\Enums\ConvertTypeEnum;
use App\Common\Enums\OrderStatusEnums;
use App\Datas\OrderData;
use App\Models\OrderModel;
use App\Services\ProductService;
use Illuminate\Support\Facades\DB;


class CompleteOrderActionMatchService extends UserActionMatchService
{

    protected $convertType = ConvertTypeEnum::PAY;


    public function __construct(){
        parent::__construct();

        $model = new OrderModel();
        $this->setModel($model);

    }



    public function getQuery(){
        $before = $this->getMatchCycleTime();

        return $this->model
            ->select(DB::raw("orders.*"))
            ->leftJoin('n8_union_users AS u','orders.uuid','=','u.id')
            ->where('u.adv_alias',$this->advAlias)
            ->where('orders.status',OrderStatusEnums::COMPLETE)
            ->when($this->timeRange,function ($query){
                $query->whereBetween('orders.complete_time',$this->timeRange);
            })
            ->where('orders.complete_click_id',0)
            ->where('u.channel_id','>',0)
            ->whereRaw(" (orders.complete_last_match_time IS NULL OR orders.complete_last_match_time <= '{$before}')")
            ->orderBy('orders.complete_time');
    }


    public function getConvertMatchData($item,$unionUser){
        return array(
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
                'click_source'  => $this->getAdvClickSourceEnum($unionUser['matcher']),
                'product_type'  => ProductService::readToType($unionUser['product_id'])
            ]
        );
    }



    public function updateActionData($match){

        $updateData = [
            'complete_last_match_time'  => date('Y-m-d H:i:s')
        ];

        if($match['click_id'] > 0){
            $updateData['complete_click_id'] = $match['click_id'];
        }

        $where = ['n8_goid'=> $match['convert_id']];
        (new OrderData())->update($where,$updateData);
    }



}
