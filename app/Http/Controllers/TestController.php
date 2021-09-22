<?php

namespace App\Http\Controllers;

use App\Common\Controllers\Front\FrontController;


use App\Common\Enums\AdvAliasEnum;
use App\Datas\N8UnionUserData;
use App\Models\N8UnionUserModel;
use App\Services\SaveUserAction\SaveFollowActionService;
use App\Services\SaveUserAction\SaveReadActionService;
use App\Services\SaveUserAction\SaveRegActionService;
use App\Services\Yw\BookService;
use App\Services\Yw\ChapterService;
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
        $this->demo();

    }

    public function demo(){
        $list = (new N8UnionUserModel())
            ->whereBetween('created_time',['2021-09-19 00:00:00','2021-09-23 00:00:00'])
            ->where('channel_id','53374')
            ->get();
        $modelData = new N8UnionUserData();
        foreach ($list as $item){
            $modelData->update(['id' => $item->id],[
                'adv_alias' => AdvAliasEnum::BD,
                'click_id'  => 0
            ]);
            echo $item->id. "\n";
        }

    }




}
