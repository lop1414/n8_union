<?php


namespace App\Http\Controllers\Admin;


use App\Models\N8UnionUserModel;

class N8UnionUserController extends BaseController
{



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
        $this->curdService->selectQueryAfter(function(){

            foreach ($this->curdService->responseData['list'] as $item){
                $item->extend;
            }
        });
    }



    /**
     * 列表预处理
     */
    public function getPrepare(){

        $this->curdService->getQueryAfter(function(){
            foreach ($this->curdService->responseData as $item){
                $item->extend;
            }
        });
    }




    /**
     * 详情预处理
     */
    public function readPrepare(){

        $this->curdService->findAfter(function(){

            $this->curdService->responseData->extend;
        });
    }




}
