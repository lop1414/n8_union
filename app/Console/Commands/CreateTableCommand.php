<?php

namespace App\Console\Commands;

use App\Common\Console\BaseCommand;
use App\Common\Helpers\Functions;
use App\Common\Services\ConsoleEchoService;
use App\Datas\N8UnionUserData;
use App\Datas\UserData;
use App\Models\N8UnionUserModel;
use App\Services\CreateTableService;
use Illuminate\Support\Facades\DB;

class CreateTableCommand extends BaseCommand
{
    /**
     * 命令行执行命令
     * @var string
     */
    protected $signature = 'create_table {--date=}';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '创建表';

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
        $this->delUnionUser();
        $service = new CreateTableService();

        $date    = $this->option('date');

        $dateList = [];
        if(!empty($date)){
            $dateRange = Functions::getDateRange($date);

            $dateList = Functions::getMonthListByRange($dateRange,'Ym');
        }else{
            $dateList[] = date('Ym',strtotime('+1 month'));
        }

        foreach ($dateList as $item){
            echo " 创建:{$item}\n";
            $service->setSuffix($item);
            $service->create();
        }

    }



    public function delUnionUser(){
        $timeRange = ['2021-03-01 00:00:00','2021-04-01 00:00:00'];
        $unionUserModel = new N8UnionUserModel();
        do{
            $list = $unionUserModel
                ->select('id','n8_guid','created_time',DB::Raw("COUNT(*) count"))
                ->whereBetween('created_time',$timeRange)
                ->groupBy('n8_guid')
                ->havingRaw('count > ?', [1])
                ->skip(0)
                ->take(100)
                ->get();

            foreach ($list as $item){
                $unionUserModel
                    ->where('n8_guid',$item['guid'])
                    ->where('created_time',$item['created_time'])
                    ->where('channel_id',0)
                    ->delete();

                echo "删除成功: {$item->id}";
            }

        }while(!$list->isEmpty());
    }





    public function updateInfo(){
        $timeRange = ['2020-09-19 00:00:00','2021-01-01 00:00:00'];
        $unionUserModel = new N8UnionUserModel();
        $unionUserModelData = new N8UnionUserData();
        $userModelData = new UserData();
        do{
            $list = $unionUserModel
                ->leftJoin('channels','channels.id','=','n8_union_users.channel_id')
                ->select('n8_union_users.*')
                ->whereBetween('n8_union_users.created_time',$timeRange)
                ->where('n8_union_users.channel_id','>',0)
                ->whereRaw('n8_union_users.created_time < channels.create_time')
                ->skip(0)
                ->take(100)
                ->get();

            foreach ($list as $item){
                $unionUserModelData->update([
                    'id'    => $item->id
                ],[
                    'channel_id'    => 0,
                    'book_id'       => 0,
                    'chapter_id'    => 0,
                    'force_chapter_id'=> 0,
                    'admin_id'      => 0,
                    'adv_alias'     => '',
                    'click_id'      => 0,
                    'last_match_time'=> null,
                ]);

                $userModelData->update([
                    'n8_guid'   => $item->n8_guid
                ],[
                    'channel_id'    => 0
                ]);

                echo "更新成功: {$item->id}";
            }

        }while(!$list->isEmpty());



    }


}
