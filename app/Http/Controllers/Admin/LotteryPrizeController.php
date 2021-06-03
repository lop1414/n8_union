<?php

namespace App\Http\Controllers\Admin;

use App\Common\Enums\StatusEnum;
use App\Common\Tools\CustomException;
use App\Enums\PrizeTypeEnum;
use App\Models\LotteryModel;
use App\Models\LotteryPrizeModel;
use Illuminate\Http\Request;

class LotteryPrizeController extends BaseController
{
    /**
     * @var string
     * 默认排序
     */
    protected $defaultOrderBy = 'order';

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
        $this->curdService->addField('order')->addDefaultValue(99);

        $this->saveHandle();

        $this->curdService->saveBefore(function(){
            $lottery = LotteryModel::find($this->curdService->requestData['lottery_id']);
            if($lottery->lottery_prizes()->count() > 10){
                throw new CustomException([
                    'code' => 'LOTTERY_PRIZE_MORE_THAN_10',
                    'message' => '奖品不能超过10个',
                ]);
            }
        });
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

    /**
     * @param Request $request
     * @return mixed
     * @throws CustomException
     * 更新排序
     */
    public function updateOrder(Request $request){
        $this->validRule($request->post(), [
            'ids' => 'required|array',
        ]);

        $ids = $request->post('ids');
        $array = array_reverse($ids);

        $order = 100;
        foreach($array as $id){
            $lotteryPrize = LotteryPrizeModel::find($id);

            if(empty($lotteryPrize)){
                continue;
            }

            $lotteryPrize->order = $order;
            $lotteryPrize->save();

            $order += 5;
        }

        return $this->success();
    }
}
