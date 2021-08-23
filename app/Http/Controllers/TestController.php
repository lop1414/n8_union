<?php

namespace App\Http\Controllers;

use App\Common\Controllers\Front\FrontController;


use App\Services\SaveUserAction\SaveAddShortcutActionService;
use App\Services\SaveUserAction\SaveCompleteOrderActionService;
use App\Services\SaveUserAction\SaveFollowActionService;
use App\Services\SaveUserAction\SaveOrderActionService;
use App\Services\SaveUserAction\SaveRegActionService;
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

//        (new SaveRegActionService())->run();
//        (new SaveAddShortcutActionService())->run();
//        (new SaveFollowActionService())->run();
//        (new SaveOrderActionService())->run();
        (new SaveCompleteOrderActionService())->run();

    }




}
