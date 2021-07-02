<?php

namespace App\Console\Commands;

use App\Common\Console\BaseCommand;
use App\Common\Helpers\Functions;
use App\Common\Services\ConsoleEchoService;
use App\Models\N8UnionUserExtendModel;
use App\Models\N8UnionUserModel;
use App\Services\CreateTableService;
use App\Services\UnionUserService;
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
        $sql = <<<STR
SELECT
u.n8_guid,u.click_id,u.created_time,e.request_id,c.click_at,c.id cid
FROM
	n8_union.n8_union_users u
	LEFT JOIN n8_union.n8_union_user_extends e ON u.id = e.uuid
	LEFT JOIN n8_adv_ocean.clicks c ON c.request_id = e.request_id
WHERE
	u.click_id = 0
	AND created_time BETWEEN '2021-06-01 00:00:00' AND '2021-07-01 00:00:00'
	AND e.request_id != ''
	AND c.click_at IS NOT NULL
	AND u.created_time < c.click_at
STR;
        $list = DB::select($sql);

        foreach ($list as $item){
            DB::update('update n8_adv_ocean.clicks set click_at = ? where id = ?', [$item->created_time,$item->cid]);
            echo $item->n8_guid. "\n";die;
        }
    }






}
