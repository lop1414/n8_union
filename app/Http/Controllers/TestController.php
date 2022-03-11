<?php

namespace App\Http\Controllers;

use App\Common\Controllers\Front\FrontController;
use App\Common\Sdks\UaRead\UaReadSdk;
use App\Common\Services\SystemApi\UnionApiService;
use App\Models\ProductModel;
use App\Sdks\Yw\YwSdk;
use App\Services\DeviceNetworkLicenseService;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

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
