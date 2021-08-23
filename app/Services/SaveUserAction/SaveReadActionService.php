<?php

namespace App\Services\SaveUserAction;


use App\Enums\QueueEnums;
use App\Models\UserReadActionModel;


class SaveReadActionService extends SaveUserActionService
{


    protected $queueEnum = QueueEnums::USER_READ_ACTION;


    public function __construct(){
        parent::__construct();
        $model = new UserReadActionModel();
        $this->setModel($model);
    }



    public function item($user,$data){

        //union用户
        $unionUser = $this->n8UnionUserService->updateSave($user,$data);

        $createData = array_merge([
            'n8_guid'       => $data['n8_guid'],
            'uuid'          => $unionUser['id'],
            'product_id'    => $data['product_id'],
            'action_time'   => $data['action_time'],
            'channel_id'    => $data['channel_id'],
            'adv_alias'     => $data['adv_alias'],
            'cp_book_id'    => $data['cp_book_id'] ?? '',
            'cp_chapter_id' => $data['cp_chapter_id'] ?? '',
            'created_at'    => date('Y-m-d H:i:s')
        ],$this->n8UnionUserService->filterDeviceInfo($data));

        $this->getModel()->setTableNameWithMonth($createData['action_time'])->create($createData);

    }

}
