<?php

namespace App\Console\Commands;

use App\Common\Console\BaseCommand;
use App\Common\Helpers\Functions;
use App\Common\Services\ConsoleEchoService;
use App\Models\N8UnionUserExtendModel;
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


    /**
     * 同步转发系统的request_id
     */
    public function demo(){
        $sql = <<<STR
SELECT
	u.n8_guid,u.click_id,l.request_id l_request_id,e.request_id e_request_id,e.uuid
FROM
	n8_union.n8_union_users u
	LEFT JOIN n8_union_user_extends e ON u.id = e.uuid
	LEFT JOIN n8_global_users g ON g.n8_guid = u.n8_guid
	LEFT JOIN n8_transfer.user_action_logs_202106 l ON g.open_id = l.open_id AND g.product_id = l.product_id
WHERE
	u.created_time >= '2021-06-01 00:00:00'
	AND e.request_id = ''
	AND l.request_id != ''
STR;
        $list = DB::select($sql);
        $model = new N8UnionUserExtendModel();

        foreach ($list as $item){
            if(empty($item->e_request_id)){
                $model->where('uuid',$item->uuid)->update(['request_id' => $item->l_request_id]);
                echo "更新成功: {$item->n8_guid}\n";
            }
        }

    }


}
