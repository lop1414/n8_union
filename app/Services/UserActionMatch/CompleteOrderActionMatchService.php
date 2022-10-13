<?php

namespace App\Services\UserActionMatch;


use App\Common\Enums\ConvertTypeEnum;
use App\Common\Enums\OrderStatusEnums;
use App\Datas\OrderData;
use App\Models\OrderModel;
use Illuminate\Support\Facades\DB;


class CompleteOrderActionMatchService extends UserActionMatchService
{

    protected $convertType = ConvertTypeEnum::PAY;


    /**
     * 上次匹配时间字段
     * @var string
     */
    protected $lastMatchTimeField = 'complete_last_match_time';


    public function __construct(){
        parent::__construct();

        $model = new OrderModel();
        $this->setModel($model);

    }



    public function getQuery($param = []){
        $before = $this->getMatchCycleTime();

        $query = $this->model
            ->select(DB::raw("orders.*"))
            ->leftJoin('n8_union_users AS u','orders.uuid','=','u.id')
            ->where('u.adv_alias',$this->advAlias)
            ->where('orders.status',OrderStatusEnums::COMPLETE)
            ->where('u.channel_id','>',0)
            ->whereRaw(" (orders.complete_last_match_time IS NULL OR orders.complete_last_match_time <= '{$before}')")
            ->orderBy('orders.complete_time');

        if(isset($param['n8_guid'])){
            return $query->where('n8_guid',$param['n8_guid']);
        }

        return $query
            ->where('u.click_id','>',0)
            ->where('orders.complete_click_id',0)
            ->when($this->timeRange,function ($query){
                $query->whereBetween('orders.complete_time',$this->timeRange);
            });
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
            'n8_union_user'=> $this->filterUnionUser($item,$unionUser)
        );
    }



    public function updateActionData($match){

        if($match['click_id'] <= 0){
            return ;
        }

        $where = ['n8_goid' => $match['convert_id']];
        (new OrderData())->update($where,['complete_click_id' => $match['click_id']]);
    }



}
