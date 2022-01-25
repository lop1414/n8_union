<?php

namespace App\Services\SaveUserAction;


use App\Enums\QueueEnums;
use App\Models\UserModel;
use App\Services\UserService;


class SaveRegActionService extends SaveUserActionService
{


    public $userService;
    protected $queueEnum = QueueEnums::USER_REG_ACTION;

    protected $isCreatedUser = true;



    public function __construct(){
        parent::__construct();
        $model = new UserModel();
        $this->setModel($model);
        $this->userService = new UserService();
    }



    public function item($user,$data){

        // 创建union用户
        return $this->n8UnionUserService->updateSave($user,$data);
    }


}
