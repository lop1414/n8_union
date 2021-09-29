<?php


namespace App\Http\Controllers\Admin;


use App\Models\UserBookReadModel;

class UserBookReadController extends BaseController
{

    /**
     * @var string
     * 默认排序字段
     */
    protected $defaultOrderBy = 'start_time';

    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new UserBookReadModel();

        parent::__construct();
    }



    /**
     * 分页列表预处理
     */
    public function selectPrepare(){
        $this->curdService->addField('n8_guid')->addValidRule('required');


        $this->curdService->selectQueryAfter(function(){
            foreach ($this->curdService->responseData['list'] as $item){
                $item->book;
                $item->chapter;
            }
        });
    }


}
