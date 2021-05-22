<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PrizeTypeEnum;
use App\Model\LotteryPrizeModel;
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
     * 创建预处理
     */
    public function createPrepare(){
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
        $this->curdService->addField('chance')->addValidRule('required|integer|max:100|min:0');
        $this->curdService->addField('image_path')->addValidRule('required');

        $this->curdService->saveBefore(function(){
            $extends = [];
            if($this->curdService->requestData['prize_type'] == PrizeTypeEnum::BOOK_COIN){
                // 书币
                $this->validRule($this->curdService->requestData, ['book_coin' => 'required']);
                $extends = [
                    'book_coin' => $this->curdService->requestData['book_coin'],
                ];
            }

            $this->curdService->handleData['extends'] = $extends;
        });
    }
}
