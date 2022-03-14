<?php

namespace App\Console\Commands;

use App\Common\Console\BaseCommand;
use App\Common\Services\ConsoleEchoService;
use App\Models\N8UnionUserModel;
use App\Services\N8UnionUserService;
use Illuminate\Support\Facades\DB;


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
        $unionUserModel = (new N8UnionUserModel())
            ->select(DB::raw("n8_union_users.id,e.ua"))
            ->leftJoin('n8_union_user_extends AS e','n8_union_users.id','=','e.uuid')
            ->leftJoin('n8_union_user_ua_info AS a','n8_union_users.id','=','a.uuid')
            ->whereNull('a.ua_device_id')
            ->where('e.ua','!=','');

        $lastId = 0;

        $service = new N8UnionUserService();

        do{
            $list = $unionUserModel->where('id','>',$lastId)->limit(10000)->get();
            foreach ($list as $item){
                $lastId = $item->id;
                $service->readUaInfo($item->id,$item->ua);
            }
            echo $lastId."\n";
        }while(!$list->isEmpty());

    }



}
