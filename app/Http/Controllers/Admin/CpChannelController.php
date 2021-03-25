<?php


namespace App\Http\Controllers\Admin;


use App\Datas\CpChannelData;
use App\Models\CpChannelModel;


class CpChannelController extends BaseController
{



    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new CpChannelModel();

        parent::__construct();
    }


    /**
     * 更新预处理
     */
    public function updatePrepare(){


        // 清缓存
        $this->curdService->saveAfter(function (){
            (new CpChannelData())->setParams([
                'id'    => $this->curdService->getModel()->id
            ])->clear();
        });
    }




}
