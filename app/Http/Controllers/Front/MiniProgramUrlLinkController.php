<?php

namespace App\Http\Controllers\Front;

use App\Common\Controllers\Front\FrontController;
use App\Services\Weixin\MiniProgram\WeixinMiniProgramUrlLinkService;
use Illuminate\Http\Request;

class MiniProgramUrlLinkController extends FrontController
{
    /**
     * constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }


    public function make(Request $request){
        $requestData = $request->post();
        if(!isset($requestData['product_id'])){
            return $this->success([]);
        }

        $urlLink = (new WeixinMiniProgramUrlLinkService())->make($requestData['product_id']);

        return $this->success(['url_link' => $urlLink]);
    }
}
