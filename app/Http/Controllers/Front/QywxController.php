<?php

namespace App\Http\Controllers\Front;

use App\Common\Controllers\Front\FrontController;
use App\Common\Enums\ExceptionTypeEnum;
use App\Common\Services\ErrorLogService;
use Illuminate\Http\Request;

class QywxController extends FrontController
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
     * 消息
     */
    public function msg(Request $request){
        $requestData = $request->all();

        $input = file_get_contents("php://input");

        $xml = simplexml_load_string($input);
        $xmlData = [];
        foreach ($xml as $k => $v) {
            $xmlData[(string) $k] = (string) $v;
        }

        $errorLogService = new ErrorLogService();
        $errorLogService->create('QYWX_MSG_LOG', '企业微信消息日志', [
            'request_data' => $requestData,
            'input' => $input,
            'xml_data' => $xmlData,
        ], ExceptionTypeEnum::CUSTOM);

        return $this->success();
    }
}
