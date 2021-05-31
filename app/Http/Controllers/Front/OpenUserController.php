<?php

namespace App\Http\Controllers\Front;

use App\Common\Controllers\Front\FrontController;
use App\Common\Tools\CustomException;
use App\Services\OpenUserService;
use Illuminate\Http\Request;

class OpenUserController extends FrontController
{
    /**
     * constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws CustomException
     * 绑定
     */
    public function bind(Request $request){
        $requestData = $request->post();

        $openUserService = new OpenUserService();
        $ret = $openUserService->bind($requestData);

        return $this->ret($ret);
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws CustomException
     * 信息
     */
    public function info(Request $request){
        $requestData = $request->post();

        $openUserService = new OpenUserService();
        $info = $openUserService->info($requestData);

        return $this->success($info);
    }
}
