<?php

namespace App\Http\Controllers;

use App\Common\Controllers\Front\FrontController;
use App\Common\Services\SystemApi\UnionApiService;
use App\Models\ProductModel;
use App\Sdks\Yw\YwSdk;
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

        $a = (new UnionApiService())->apiReadChannel(['id' => 6178]);
        dd($a);
    }



}
