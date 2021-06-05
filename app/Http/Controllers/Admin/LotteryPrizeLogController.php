<?php

namespace App\Http\Controllers\Admin;

use App\Models\LotteryPrizeLogModel;
use Illuminate\Http\Request;

class LotteryPrizeLogController extends BaseController
{
    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new LotteryPrizeLogModel();

        parent::__construct();
    }

    /**
     * 列表预处理
     */
    public function selectPrepare(){
        $this->curdService->selectQueryAfter(function(){
            foreach($this->curdService->responseData['list'] as $k => $v){
                $this->curdService->responseData['list'][$k] = $this->model->expandExtendsField($v);
            }
        });
    }

    /**
     * 详情预处理
     */
    public function readPrepare(){
        $this->curdService->findAfter(function(){
            $this->curdService->findData = $this->model->expandExtendsField($this->curdService->findData);
        });
    }
}
