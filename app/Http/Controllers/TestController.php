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



    }

}
