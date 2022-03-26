<?php

namespace App\Http\Controllers;

use App\Common\Controllers\Front\FrontController;
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


        $product = (new ProductModel())->find(79);
        $ywSdk = new YwSdk($product['cp_product_alias'],$product['cp_account']['account'],$product['cp_account']['cp_secret']);
        $info = $ywSdk->getBookInfo(20294462201079606);
        dd($info);
    }



}
