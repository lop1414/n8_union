<?php

namespace App\Http\Controllers;

use App\Common\Controllers\Front\FrontController;


use App\Common\Enums\CpTypeEnums;
use App\Models\ProductModel;
use App\Sdks\Qy\QySdk;
use App\Services\Qy\ChannelService;
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

        $data = ['cp_channel_id' => ''];
        dd(empty($data['cp_channel_id']));


        $product = (new ProductModel())->where('cp_type',CpTypeEnums::QY)->first();

        (new ChannelService())->sync('2021-08-08','2021-08-11',$product['id'],[4943]);


    }




}
