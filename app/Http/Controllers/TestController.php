<?php

namespace App\Http\Controllers;

use App\Common\Controllers\Front\FrontController;
use App\Datas\BookData;
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
        $info = (new BookData())->setParams(['cp_type' => 'YW', 'cp_book_id' => 21520039808040206])->read();
        dd($info);
    }

}
