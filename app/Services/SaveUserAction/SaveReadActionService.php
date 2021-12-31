<?php

namespace App\Services\SaveUserAction;


use App\Common\Tools\CustomException;
use App\Enums\QueueEnums;
use App\Models\UserReadActionModel;
use App\Services\Cp\CpProviderService;
use App\Services\N8UnionUserService;
use App\Services\UserBookReadService;


class SaveReadActionService extends SaveUserActionService
{


    protected $queueEnum = QueueEnums::USER_READ_ACTION;

    protected $unionUserService;

    protected $userBookReadService;



    public function __construct(){
        parent::__construct();
        $model = new UserReadActionModel();
        $this->setModel($model);
        $this->unionUserService = new N8UnionUserService();
        $this->userBookReadService = new UserBookReadService();
    }



    public function item($user,$data){

        if(empty($user)){
            throw new CustomException([
                'code'    => 'NOT_USER',
                'message' => '没有用户',
                'log'     => false,
                'data'    => ['n8_guid' => $data['n8_guid']]
            ]);
        }

        $unionUser = $this->unionUserService->read($user['n8_guid'],$user['channel_id']);

        $cpBookService = CpProviderService::readCpBookService($data['cp_type']);
        $cpChapterService = CpProviderService::readCpChapterService($data['cp_type']);
        if(is_null($cpBookService) || is_null($cpChapterService)){
            throw new CustomException([
                'code'    => 'NOT_SERVICE',
                'message' => '该书城没有实现BookService 或 ChapterServices',
                'log'     => true,
                'data'    => $data
            ]);
        }

        $book = $cpBookService->readSave($data['cp_book_id'],$data['cp_book_name']);
        $chapter = $cpChapterService->setBook($book)->readSave($data['cp_chapter_id'],$data['cp_chapter_name'],$data['cp_chapter_index']);


        $createData = [
            'uuid' => $unionUser['id'],
            'book_id'   => $book['id'],
            'chapter_id' => $chapter['id'],
            'created_at' => date('Y-m-d H:i:s'),
            'n8_guid'    => $data['n8_guid'],
            'action_time'=> $data['action_time'],
            'extends'    => $data['extends']
        ];

        $info = $this->getModel()->setTableNameWithMonth($createData['action_time'])->create($createData);
        $this->userBookReadService->analysis($info);

        return $unionUser;
    }

}
