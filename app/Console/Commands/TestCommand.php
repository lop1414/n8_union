<?php

namespace App\Console\Commands;

use App\Common\Console\BaseCommand;
use App\Common\Enums\AdvAliasEnum;
use App\Common\Enums\OrderStatusEnums;
use App\Common\Helpers\Functions;
use App\Common\Services\ConsoleEchoService;
use App\Datas\ChannelData;
use App\Datas\ChannelExtendData;
use App\Models\N8UnionUserModel;
use App\Models\OrderModel;
use App\Models\UserReadActionModel;
use App\Services\N8UnionUserService;
use App\Services\UserBookReadService;

class TestCommand extends BaseCommand
{
    /**
     * 命令行执行命令
     * @var string
     */
    protected $signature = 'test';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '';

    protected $consoleEchoService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(){
        parent::__construct();
        $this->consoleEchoService = new ConsoleEchoService();
    }



    public function handle(){
        $this->userBookRead();
    }


    public function userBookRead(){
        $tableList = ['user_read_actions_202109'];
        $services = new UserBookReadService();
        foreach ($tableList as $tableName){
            echo $tableName ."\n\n\n\n";
            $lastId = 4808788;
            do{
                $list = (new UserReadActionModel())
                    ->setTable($tableName)
                    ->where('id','>',$lastId)
                    ->skip(0)
                    ->take(1000)
                    ->orderBy('id')
                    ->get();
                foreach ($list as $item){
                    $lastId = $item->id;
                    echo "\r".$lastId;
                    $services->analysis($item);
                }

            }while(!$list->isEmpty());
        }
    }




    // 更新订单下单次数 完成次数
    public function updateOrderTimes(){
        $startTime = '2021-07-01 00:00:00';
        $endTime = '2021-07-02 00:00:00';

        $list = (new OrderModel())
            ->whereBetween('order_time',[$startTime,$endTime])
            ->get();
        foreach ($list as $item){
            $orderTimes = (new OrderModel())
                ->where('n8_guid',$item['n8_guid'])
                ->where('channel_id',$item['channel_id'])
                ->where('order_time','<',$item['order_time'])
                ->count();

            $completeTimes = (new OrderModel())
                ->where('n8_guid',$item['n8_guid'])
                ->where('channel_id',$item['channel_id'])
                ->where('status',OrderStatusEnums::COMPLETE)
                ->where('order_time','<',$item['order_time'])
                ->count();

            $item->order_times = $orderTimes + 1;
            $item->complete_times = $item['status'] == OrderStatusEnums::COMPLETE ? $completeTimes + 1 : $completeTimes;
            $item->save();
        }



    }


}
