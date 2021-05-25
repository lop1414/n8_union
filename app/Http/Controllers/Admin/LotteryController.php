<?php

namespace App\Http\Controllers\Admin;

use App\Common\Enums\CycleTypeEnum;
use App\Common\Helpers\Functions;
use App\Common\Tools\CustomException;
use App\Models\LotteryModel;
use App\Services\LotteryService;
use Illuminate\Http\Request;

class LotteryController extends BaseController
{
    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new LotteryModel();

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
        $this->curdService->addField('name')->addValidRule('required');
        $this->curdService->addField('description')->addValidRule('required');
        $this->curdService->addField('cycle_type')->addValidRule('required')
            ->addValidEnum(CycleTypeEnum::class);
        $this->curdService->addField('max_times')->addValidRule('required|integer');
        $this->curdService->addField('start_at')->addValidRule('required');
        $this->curdService->addField('end_at')->addValidRule('required');

        $this->curdService->saveBefore(function(){
            $this->curdService->handleData['description'] = Functions::filterEmoji($this->curdService->requestData['description']);

            if(!Functions::timeCheck($this->curdService->requestData['start_at'])){
                throw new CustomException([
                    'code' => 'START_AT_ERROR',
                    'message' => '开始时间格式错误',
                ]);
            }

            if(!Functions::timeCheck($this->curdService->requestData['end_at'])){
                throw new CustomException([
                    'code' => 'END_AT_ERROR',
                    'message' => '结束时间格式错误',
                ]);
            }

            if($this->curdService->requestData['start_at'] > $this->curdService->requestData['end_at']){
                throw new CustomException([
                    'code' => 'START_AT_MORE_THAN_END_AT',
                    'message' => '开始时间不能大于结束时间',
                ]);
            }

            $this->curdService->handleData['extends'] = [];
        });
    }

    public function release(Request $request){
        $this->validRule($request->post(), [
            'lottery_id' => 'required|integer',
        ]);

        $lotteryId = $request->post('lottery_id');

        $lotteryService = new LotteryService();
        $ret = $lotteryService->release($lotteryId);

        return $this->ret($ret);
    }
}
