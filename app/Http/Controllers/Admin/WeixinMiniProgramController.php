<?php

namespace App\Http\Controllers\Admin;

use App\Common\Tools\CustomException;
use App\Models\WeixinMiniProgramModel;
use Illuminate\Http\Request;

class WeixinMiniProgramController extends BaseController
{
    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new WeixinMiniProgramModel();

        parent::__construct();
    }

    /**
     * 详情预处理
     */
    public function readPrepare(){
        $this->curdService->findAfter(function(){
            $this->curdService->findData->makeVisible('app_secret');
        });
    }

    /**
     * 创建预处理
     */
    public function createPrepare(){
        $this->saveHandle();

        $this->curdService->saveBefore(function(){
            if($this->curdService->getModel()->exist('app_id', $this->curdService->handleData['app_id'])){
                throw new CustomException([
                    'code' => 'APP_ID_EXIST',
                    'message' => 'APP_ID已存在',
                ]);
            }
        });
    }

    /**
     * 更新预处理
     */
    public function updatePrepare(){
        $this->saveHandle();

        $this->curdService->saveBefore(function() {
            if ($this->curdService->getModel()->existWithoutSelf('app_id', $this->curdService->handleData['app_id'], $this->curdService->requestData['id'])){
                throw new CustomException([
                    'code' => 'APP_ID_EXIST',
                    'message' => 'APP_ID已存在',
                ]);
            }
        });
    }

    /**
     * 保存处理
     */
    private function saveHandle(){
        $this->curdService->addField('name')->addValidRule('required');
        $this->curdService->addField('app_id')->addValidRule('required');
        $this->curdService->addField('app_secret')->addValidRule('required');
    }
}
