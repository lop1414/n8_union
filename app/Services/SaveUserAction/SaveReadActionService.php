<?php

namespace App\Services\SaveUserAction;


use App\Common\Enums\CpTypeEnums;
use App\Common\Tools\CustomException;
use App\Enums\QueueEnums;
use App\Models\UserReadActionModel;
use App\Services\N8UnionUserService;
use App\Services\Stat\UserReadStatService;
use App\Services\Yw\BookService;
use App\Services\Yw\ChapterService;


class SaveReadActionService extends SaveUserActionService
{


    protected $queueEnum = QueueEnums::USER_READ_ACTION;

    protected $unionUserService;

    protected $ywBookService;

    protected $ywChapterService;

    protected $userReadStatService;


    public function __construct(){
        parent::__construct();
        $model = new UserReadActionModel();
        $this->setModel($model);
        $this->unionUserService = new N8UnionUserService();
        $this->ywBookService = new BookService();
        $this->ywChapterService = new ChapterService();
        $this->userReadStatService = new UserReadStatService();
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

        $createData = [
            'uuid' => $unionUser['id'],
            'created_at' => date('Y-m-d H:i:s'),
            'n8_guid'    => $data['n8_guid'],
            'action_time'=> $data['action_time'],
            'extends'    => $data['extends']
        ];

        if($data['cp_type'] == CpTypeEnums::YW){
            $book = $this->ywBookService->readSave($data['cp_book_id'],$data['cp_book_name']);
            $chapter = $this->ywChapterService->setBook($book)->readSave($data['cp_chapter_id'],$data['cp_chapter_name'],$data['cp_chapter_index']);
            $createData['book_id'] = $book['id'];
            $createData['chapter_id'] = $chapter['id'];
        }

        if(!empty($createData['book_id']) && !empty($createData['chapter_id'])){
            $info = $this->getModel()->setTableNameWithMonth($createData['action_time'])->create($createData);
            $this->userReadStatService->analysis($info);
        }

        return $unionUser;
    }

}
