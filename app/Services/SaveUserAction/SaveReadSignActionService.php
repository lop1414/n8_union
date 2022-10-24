<?php

namespace App\Services\SaveUserAction;


use App\Enums\QueueEnums;
use App\Models\N8UnionUserReadSignLogModel;
use App\Services\N8UnionUserService;


class SaveReadSignActionService extends SaveUserActionService
{


    protected $queueEnum = QueueEnums::USER_READ_SIGN_ACTION;

    protected $unionUserService;



    public function __construct(){
        parent::__construct();
        $model = new N8UnionUserReadSignLogModel();
        $this->setModel($model);
        $this->unionUserService = new N8UnionUserService();
    }



    public function item($user,$data){

        $unionUser = $this->unionUserService->read($user['n8_guid'],$user['channel_id']);

        $this->getModel()->create([
            'uuid'            => $unionUser['id'],
            'read_sign_type'  => $data['read_sign_type'],
            'created_time'    => $data['created_time']
        ]);

        return $unionUser;
    }

}
