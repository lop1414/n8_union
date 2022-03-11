<?php

namespace App\Console\Commands;

use App\Common\Console\BaseCommand;
use App\Common\Models\BaseModel;
use App\Common\Services\ConsoleEchoService;
use App\Models\DeviceModel;
use App\Models\N8UnionUserModel;
use App\Services\DeviceNetworkLicenseService;
use App\Services\UaReadService;
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
            ->select(DB::raw("n8_union_users.*"))
            ->leftJoin('n8_union_user_extends AS e','n8_union_users.id','=','e.uuid')
            ->where('n8_union_users.device_model','')
            ->where('e.ua','!=','');

        $lastId = 0;

        $uaReadService = new UaReadService();

        do{
            $list = $unionUserModel->where('id','>',$lastId)->limit(5000)->get();
            echo $lastId."\n";
            foreach ($list as $item){
                $lastId = $item->id;
                $uaReadInfo = $uaReadService->setUa($item->extend->ua)->getInfo();
                $item->sys_version = $uaReadInfo['sys_version'] ?? '';
                $item->device_model = $uaReadInfo['device_model'] ?? '';
                $item->save();
            }
        }while(!$list->isEmpty());

    }



}
