<?php


namespace App\Http\Controllers\Open;


use App\Common\Helpers\Functions;
use App\Common\Tools\CustomException;
use App\Common\Enums\OrderTypeEnums;
use App\Enums\QueueEnums;
use App\Common\Services\DataToQueueService;
use Illuminate\Http\Request;

class OrderController extends BaseController
{

    public function order(Request $request){
        $requestData = $request->all();

        // 必传参数
        $this->validRule($requestData,[
            'cp_channel_id'     =>  'required',
            'order_id'          =>  'required',
            'open_id'           =>  'required',
            'action_time'       =>  'required',
            'amount'            =>  'required',
            'type'              =>  'required',
        ]);

        //验证枚举
        Functions::hasEnum(OrderTypeEnums::class, $requestData['type']);

        $service = new DataToQueueService(QueueEnums::USER_ORDER_ACTION);
        $service->push($requestData);

        return $this->success();
    }


    /**
     * 订单完成
     *
     * @param Request $request
     * @return string[]
     * @throws CustomException
     */
    public function complete(Request $request){
        $requestData = $request->all();

        // 必传参数
        $this->validRule($requestData,[
            'order_id'          =>  'required',
            'complete_time'     =>  'required',
        ]);


        $service = new DataToQueueService(QueueEnums::USER_COMPLETE_ORDER_ACTION);
        $service->push($requestData);
        return $this->success();
    }




}
