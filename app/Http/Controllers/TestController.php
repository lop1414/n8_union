<?php

namespace App\Http\Controllers;

use App\Common\Controllers\Front\FrontController;
use App\Datas\BookData;
use App\Models\ProductModel;
use App\Sdks\Fq\FqSdk;
use App\Services\Cp\Book\FqBookService;
use App\Services\Cp\Channel\FqChannelService;
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

$this->demo($productInfo);
        $service = new FqChannelService();
        $service->setProduct($productInfo);
        $service->setParam('start_date','2021-11-11');
        $service->setParam('end_date','2022-01-11');
        $service->setParam('channel_ids',[104,105]);
        $service->sync();
//        $service->sync();
    }

    public function demo($product){
        $sdk = new FqSdk($product['cp_product_alias'],$product['cp_secret']);
        $a = $sdk->getUsers('2021-11-11','2022-01-11');
        dd($a);
    }

}
