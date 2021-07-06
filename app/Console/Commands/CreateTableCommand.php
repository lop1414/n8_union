<?php

namespace App\Console\Commands;

use App\Common\Console\BaseCommand;
use App\Common\Enums\PlatformEnum;
use App\Common\Helpers\Functions;
use App\Common\Services\ConsoleEchoService;
use App\Datas\N8UnionUserData;
use App\Models\N8UnionUserModel;
use App\Models\UserExtendModel;
use App\Services\CreateTableService;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;


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
        $this->demo();die;
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



    public function demo(){
        $lastMaxId = 0;
        do{
            $list = (new N8UnionUserModel())
                ->leftJoin('n8_global_users AS g','g.n8_guid','n8_union_users.n8_guid')
                ->select(DB::raw('n8_union_users.id,n8_union_users.channel_id,n8_union_users.platform,n8_union_users.click_id,g.*'))
                ->where('n8_union_users.platform','')
                ->where('n8_union_users.id','>',$lastMaxId)
                ->take(1000)
                ->get();
            foreach ($list as $item){
                $lastMaxId = $item['id'];
                $ua = $item->ua ?: $this->getUserUa($item['n8_guid'],$item['open_id']);
                if(empty($ua)) continue;

                $agent = new Agent();
                $agent->setUserAgent($ua);
                $platform = $agent->isiOS() ? PlatformEnum::IOS : PlatformEnum::ANDROID;

                (new N8UnionUserData())->update(['id'=>$item['id']],['platform'=>$platform]);
            }


        }while(!$list->isEmpty());
    }

    public function getUserUa($n8Guid,$openId){
        $info = DB::connection('novel_admin')->select("SELECT * FROM novel_users WHERE open_id = '{$openId}' AND ua != ''");
        if(empty($info)) return '';

        return $info[0]->ua;
    }








}
