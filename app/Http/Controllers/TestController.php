<?php

namespace App\Http\Controllers;

use App\Common\Controllers\Front\FrontController;
use App\Datas\BookData;
use App\Models\ProductModel;
use App\Sdks\Fq\FqSdk;
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
        $productInfo = (new ProductModel())->where('cp_product_alias','1715568083108909')->first();
//        dd($productInfo);
        $list = (new FqSdk($productInfo->cp_product_alias,$productInfo->cp_secret))
            ->getOrders('2021-11-11 00:00:00','2022-11-20 00:00:00');
        dd($list);
    }

}
