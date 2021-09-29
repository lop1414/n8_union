<?php

namespace App\Http\Controllers;

use App\Common\Controllers\Front\FrontController;


use App\Common\Enums\AdvAliasEnum;
use App\Datas\ChannelExtendData;
use App\Datas\N8UnionUserData;
use App\Models\N8UnionUserModel;
use App\Models\UserBookReadModel;
use App\Services\N8UnionUserService;
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
        $info = (new UserBookReadModel())->create([
            'n8_guid' => 1,
            'book_id' => 2,
            'last_chapter_id' => 1,
            'start_time' => date('Y-m-d H:i:s'),
            'last_time' => date('Y-m-d H:i:s')
        ]);
        dd($info);
    }

}
