<?php


namespace App\Http\Controllers\Admin;


use App\Common\Enums\ConvertTypeEnum;
use App\Models\N8UnionUserModel;

class N8UnionUserController extends UserActionBaseController
{

    public $convertType = ConvertTypeEnum::REGISTER;

    public $mapUnionUser = false;


    /**
     * @var string
     * 默认排序字段
     */
    protected $defaultOrderBy = 'created_time';


    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new N8UnionUserModel();

        parent::__construct();
    }




    /**
     * 分页列表预处理
     */
    public function selectPrepare(){

        $this->selectCommonPrepare();
    }


    public function itemPrepare($item){
        $item->extend;
        $item->book;
        $item->chapter;
        $item->force_chapter;
    }


    /**
     * 详情预处理
     */
    public function readPrepare(){
        $this->readCommonPrepare();
        $this->curdService->findAfter(function(){
            $this->curdService->responseData->extend;
            $this->curdService->responseData->book;
            $this->curdService->responseData->chapter;
            $this->curdService->responseData->force_chapter;
        });
    }




}
