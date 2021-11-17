<?php

namespace App\Services\SaveUserAction;


use App\Common\Enums\CpTypeEnums;
use App\Common\Tools\CustomException;
use App\Enums\QueueEnums;
use App\Models\UserReadActionModel;
use App\Services\Cp\Book\QyBookService;
use App\Services\Cp\Book\YwBookService;
use App\Services\Cp\Chapter\QyChapterService;
use App\Services\Cp\Chapter\YwChapterService;
use App\Services\N8UnionUserService;
use App\Services\UserBookReadService;


class SaveReadActionService extends SaveUserActionService
{


    protected $queueEnum = QueueEnums::USER_READ_ACTION;

    protected $unionUserService;

    protected $userBookReadService;

    protected $cpBookServices,$cpChapterServices;


    public function __construct(){
        parent::__construct();
        $model = new UserReadActionModel();
        $this->setModel($model);
        $this->unionUserService = new N8UnionUserService();
        $this->userBookReadService = new UserBookReadService();
        $this->cpBookServices = [
            CpTypeEnums::YW => new YwBookService(),
            CpTypeEnums::QY => new QyBookService()
        ];

        $this->cpChapterServices = [
            CpTypeEnums::YW => new YwChapterService(),
            CpTypeEnums::QY => new QyChapterService()
        ];
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

        if(empty($this->cpBookServices[$data['cp_type']]) || empty($this->cpChapterServices[$data['cp_type']])){
            throw new CustomException([
                'code'    => 'NOT_SERVICE',
                'message' => '该书城没有实现BookService 或 ChapterServices',
                'log'     => true,
                'data'    => $data
            ]);
        }

        $book = $this->cpBookServices[$data['cp_type']]
            ->readSave($data['cp_book_id'],$data['cp_book_name']);

        $chapter = $this->cpChapterServices[$data['cp_type']]
            ->setBook($book)
            ->readSave($data['cp_chapter_id'],$data['cp_chapter_name'],$data['cp_chapter_index']);


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
