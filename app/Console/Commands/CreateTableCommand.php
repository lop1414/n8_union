<?php

namespace App\Console\Commands;

use App\Common\Console\BaseCommand;
use App\Common\Helpers\Functions;
use App\Common\Services\ConsoleEchoService;
use App\Models\N8UnionUserExtendModel;
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
        $service = new CreateTableService();
$this->demo2();die;
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


    public function demo2(){
        $sql = <<<STR
SELECT
	u.id,u.n8_guid,u.product_id,u.channel_id,u.created_time,u.adv_alias,f.id fid,s.id sid,o.n8_goid
FROM
	n8_union_users u
	LEFT JOIN user_follow_actions f ON f.uuid = u.id
	LEFT JOIN user_shortcut_actions s ON s.uuid = u.id
	LEFT JOIN orders o ON o.uuid = u.id
WHERE
	u.n8_guid IN (
		SELECT n8_guid
		FROM (
				SELECT count(*) c,n.n8_guid
				FROM n8_union_users n
				LEFT JOIN products p ON p.id = n.product_id
				WHERE p.type = 'H5' AND n.created_time BETWEEN '2021-05-01 00:00:00' AND '2021-07-01 00:00:00'
				GROUP BY n.n8_guid HAVING c > 1
			) a
	)
	AND u.channel_id  = 0
	AND f.id IS NULL
	AND s.id IS NULL
	AND o.n8_goid IS NULL
ORDER BY n8_guid
STR;
        $list = DB::select($sql);
        $model = new N8UnionUserModel();
        $extendModel = new N8UnionUserExtendModel();

        foreach ($list as $item){
            $info = $model->where('id',$item->id)->first();

            $changeInfo = $model
                ->select(DB::raw('n8_union_user_extends.*'))
                ->leftJoin('n8_union_user_extends AS e','n8_union_users.id','=','e.uuid')
                ->where('n8_union_users.n8_guid',$item->n8_guid)
                ->where('e.ip','')
                ->first();
            // 补ip
            if(!empty($info->extend['ip']) && empty($changeInfo['ip'])){
                $extendModel->where('uuid',$changeInfo['uuid'])->update([
                    'ip'    => $info->extend['ip']
                ]);
            }

            //删除
            $extendModel->where('uuid',$info['id'])->delete();
            $info->delete();

            echo "成功: {$info->id}\n"; die;
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
