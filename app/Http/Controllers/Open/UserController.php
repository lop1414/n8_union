<?php


namespace App\Http\Controllers\Open;


use App\Common\Tools\CustomException;
use App\Enums\QueueEnums;
use App\Common\Services\DataToQueueService;
use Illuminate\Http\Request;

class UserController extends BaseController
{

    /**
     * 注册
     *
     * @param Request $request
     * @return mixed
     * @throws CustomException
     */
    public function reg(Request $request){
        $requestData = $request->all();

        // 必传参数
        $this->validRule($requestData,[
            'open_id'       =>  'required',
            'action_time'   =>  'required'
        ]);
        $requestData['cp_channel_id'] = $requestData['cp_channel_id'] ?? 0;


        $service = new DataToQueueService(QueueEnums::USER_REG_ACTION);
        $service->push($requestData);

        return $this->success();
    }


    /**
     * 阅读行为
     *
     * @param Request $request
     * @return mixed
     * @throws CustomException
     */
    public function read(Request $request){
        $requestData = $request->all();

        // 必传参数
        $this->validRule($requestData,[
            'open_id'       =>  'required',
            'action_time'   =>  'required'
        ]);
        $requestData['cp_channel_id'] = $requestData['cp_channel_id'] ?? 0;

        $service = new DataToQueueService(QueueEnums::USER_READ_ACTION);
        $service->push($requestData);

        return $this->success();
    }



    /**
     * 登陆行为
     *
     * @param Request $request
     * @return mixed
     * @throws CustomException
     */
    public function login(Request $request){
        $requestData = $request->all();

        // 必传参数
        $this->validRule($requestData,[
            'open_id'           =>  'required',
            'action_time'       =>  'required'
        ]);
        $requestData['cp_channel_id'] = $requestData['cp_channel_id'] ?? 0;


        $service = new DataToQueueService(QueueEnums::USER_LOGIN_ACTION);
        $service->push($requestData);
        return $this->success();
    }


    /**
     * 加桌行为
     *
     * @param Request $request
     * @return mixed
     * @throws CustomException
     */
    public function addShortcut(Request $request){
        $requestData = $request->all();

        // 必传参数
        $this->validRule($requestData,[
            'open_id'           =>  'required',
            'action_time'       =>  'required'
        ]);
        $requestData['cp_channel_id'] = $requestData['cp_channel_id'] ?? 0;

        $service = new DataToQueueService(QueueEnums::USER_ADD_SHORTCUT_ACTION);
        $service->push($requestData);
        return $this->success();
    }



    /**
     * 关注行为
     *
     * @param Request $request
     * @return mixed
     * @throws CustomException
     */
    public function follow(Request $request){
        $requestData = $request->all();

        // 必传参数
        $this->validRule($requestData,[
            'open_id'           =>  'required',
            'action_time'       =>  'required'
        ]);

        $service = new DataToQueueService(QueueEnums::USER_FOLLOW_ACTION);
        $requestData['cp_channel_id'] = $requestData['cp_channel_id'] ?? 0;
        $service->push($requestData);
        return $this->success();
    }

}
