<?php

namespace App\Http\Controllers;

use App\Common\Controllers\Front\FrontController;


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

         (new SaveFollowActionService())->run();
         (new SaveRegActionService())->run();


    }




}
