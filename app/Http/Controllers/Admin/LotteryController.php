<?php

namespace App\Http\Controllers\Admin;

use App\Common\Enums\CycleTypeEnum;
use App\Common\Helpers\Functions;
use App\Common\Tools\CustomException;
use App\Models\LotteryModel;
use App\Services\Activity\LotteryService;
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

    /**
     * 列表预处理
     */
    public function selectPrepare(){
        $this->curdService->selectQueryAfter(function(){
            $lotteryService = new LotteryService();
            foreach($this->curdService->responseData['list'] as $k => $v){
                // 获取发布数据
                $releaseData = $lotteryService->getReleaseData($v->id);

                // 是否发生改变
                $this->curdService->responseData['list'][$k]->has_change = json_encode($releaseData) != json_encode($v->release_data);
            }
        });
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws CustomException
     * 发布
     */
    public function release(Request $request){
        $this->validRule($request->post(), [
            'id' => 'required|integer',
        ]);

        $id = $request->post('id');

        $lotteryService = new LotteryService();
        $ret = $lotteryService->release($id);

        return $this->ret($ret);
    }
}
