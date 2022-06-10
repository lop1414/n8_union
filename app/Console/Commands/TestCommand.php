<?php

namespace App\Console\Commands;

use App\Common\Console\BaseCommand;
use App\Models\N8UnionUserModel;
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






    public function handle(){
        $unionUserModel = (new N8UnionUserModel());

        $lastId = 0;

        do{
            echo $lastId."\n";
            $list = $unionUserModel->where('id','>',$lastId)->limit(10)->get()->map(function ($user){
                $user->demo = 1;
                return $user;
            });

        }while(!$list->isEmpty());

    }



}
