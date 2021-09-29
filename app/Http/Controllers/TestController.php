<?php

namespace App\Http\Controllers;

use App\Common\Controllers\Front\FrontController;


use App\Models\UserBookReadModel;

use App\Models\UserReadActionModel;
use App\Services\UserBookReadService;
use Illuminate\Http\Request;

class TestController extends FrontController
{
    /**
     * constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }



    public function test(Request $request){
        $key = $request->input('key');
        if($key != 'aut'){
            return $this->forbidden();
        }

        $tableList = ['user_read_actions_202108','user_read_actions_202109'];
        $services = new UserBookReadService();
        foreach ($tableList as $tableName){
            $lastId = 0;
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
                    echo $lastId."\n";
                    $services->analysis($item);
                }

            }while(!$list->isEmpty());
        }

    }

}
