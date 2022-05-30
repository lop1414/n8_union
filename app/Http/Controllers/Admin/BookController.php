<?php
namespace App\Http\Controllers\Admin;


use App\Models\BookModel;

class BookController extends BaseController
{

    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new BookModel();

        parent::__construct();
    }

    /**
     * 过滤
     */
    public function dataFilter(){

        $this->curdService->customBuilder(function ($builder){
            $keyword = $req['keyword'] ?? '';
            if(!empty($keyword)){
                $builder->whereRaw(" (`name` LIKE '%{$keyword}%' OR `id` LIKE '%{$keyword}%' OR `cp_book_id` LIKE '%{$keyword}%')");
            }
        });
    }

    /**
     * 分页列表预处理
     */
    public function selectPrepare(){
        $this->curdService->selectQueryBefore(function (){
            $this->dataFilter();
        });
    }

    /**
     * 列表预处理
     */
    public function getPrepare(){
        $this->curdService->getQueryBefore(function (){
            $this->dataFilter();
        });
    }

}
