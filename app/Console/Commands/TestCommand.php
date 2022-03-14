<?php

namespace App\Console\Commands;

use App\Common\Console\BaseCommand;
use App\Common\Services\ConsoleEchoService;
use App\Models\UaDeviceModel;
use App\Services\Ua\UaDeviceService;


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
        $unionUserModel = (new UaDeviceModel());

        $lastId = 0;

        $service = new UaDeviceService();

        do{
            echo $lastId."\n";
            $list = $unionUserModel->where('id','>',$lastId)->limit(10000)->get();
            foreach ($list as $item){
                $lastId = $item->id;
                $service->update($item->model);
            }
        }while(!$list->isEmpty());

    }



}
