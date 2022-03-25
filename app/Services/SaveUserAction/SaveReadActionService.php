<?php

namespace App\Services\SaveUserAction;


use App\Enums\QueueEnums;
use App\Models\UserReadActionModel;
use App\Services\BookService;
use App\Services\ChapterService;
use App\Services\N8UnionUserService;
use App\Services\UserBookReadService;


class SaveReadActionService extends SaveUserActionService
{


    protected $queueEnum = QueueEnums::USER_READ_ACTION;

    protected $unionUserService;

    protected $userBookReadService;

    protected $bookService;

    protected $charterService;



    public function __construct(){
        parent::__construct();
        $model = new UserReadActionModel();
        $this->setModel($model);
        $this->unionUserService = new N8UnionUserService();
        $this->userBookReadService = new UserBookReadService();

        $this->bookService = new BookService();
        $this->charterService = new ChapterService();
    }



    public function item($user,$data){

        $unionUser = $this->unionUserService->read($user['n8_guid'],$user['channel_id']);

        $book = $this->bookService->readSave([
            'cp_book_id' => $data['cp_book_id'],
            'name'       => $data['cp_book_name'],
            'cp_type'    => $data['cp_type']
        ]);
        $chapter = $this->charterService->readSave($book['id'],$data['cp_chapter_id'],$data['cp_chapter_name'],$data['cp_chapter_index']);

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
