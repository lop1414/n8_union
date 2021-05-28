<?php

namespace App\Http\Controllers\Admin;

use App\Common\Enums\StatusEnum;
use App\Enums\PrizeTypeEnum;
use App\Models\LotteryPrizeModel;
use Illuminate\Http\Request;

class LotteryPrizeController extends BaseController
{
    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new LotteryPrizeModel();

        parent::__construct();
    }

    /**
     * 列表预处理
     */
    public function selectPrepare(){
        $this->curdService->selectQueryAfter(function(){
            foreach($this->curdService->responseData['list'] as $k => $v){
                $this->curdService->responseData['list'][$k] = $this->curdService->getModel()->expandExtendsField($v);
            }
        });
    }

    /**
     * 详情预处理
     */
    public function readPrepare(){
        $this->curdService->findAfter(function(){
            $this->curdService->findData = $this->curdService->getModel()->expandExtendsField($this->curdService->findData);
        });
    }

    /**
     * 创建预处理
     */
    public function createPrepare(){
        $this->curdService->addField('status')->addDefaultValue(StatusEnum::ENABLE);
        $this->saveHandle();
    }

    /**
     * 更新预处理
     */
    public function updatePrepare(){
        $this->saveHandle();
    }

    /**
     * 保存处理
     */
    private function saveHandle(){
        $this->curdService->addField('lottery_id')->addValidRule('required|integer');
        $this->curdService->addField('name')->addValidRule('required');
        $this->curdService->addField('prize_type')->addValidRule('required')
            ->addValidEnum(PrizeTypeEnum::class);
        $this->curdService->addField('chance')->addValidRule('required|max:100|min:0');
        $this->curdService->addField('total')->addValidRule('required|integer');
        $this->curdService->addField('image_url')->addValidRule('required');

        $this->curdService->saveBefore(function(){
            $extends = [];
            if($this->curdService->requestData['prize_type'] == PrizeTypeEnum::BOOK_COIN){
                // 书币
                $this->validRule($this->curdService->requestData, ['book_coin' => 'required']);
                $extends = [
                    'book_coin' => $this->curdService->requestData['book_coin'],
                ];
            }elseif($this->curdService->requestData['prize_type'] == PrizeTypeEnum::NOTHING){
                $this->curdService->handleData['total'] = -1;
            }

            $this->curdService->handleData['extends'] = $extends;
        });
    }
}
