<?php

namespace App\Http\Controllers\Admin;

use App\Models\LotteryPrizeLogModel;
use App\Services\OpenUserService;
use App\Services\ProductService;

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
                $this->curdService->responseData['list'][$k] = $this->format($v);
            }
        });
    }

    /**
     * 详情预处理
     */
    public function readPrepare(){
        $this->curdService->findAfter(function(){
            $this->curdService->findData = $this->format($this->curdService->findData);
        });
    }

    /**
     * @param $item
     * @return mixed
     * @throws \App\Common\Tools\CustomException
     * 修饰
     */
    private function format($item){
        $item = $this->model->expandExtendsField($item);

        // 关联用户
        $openUserService = new OpenUserService();
        $item['n8_global_user'] = $openUserService->getGlobalUserByGuid($item->n8_guid);

        // 关联产品
        $n8GlobalUser = $item['n8_global_user'];
        $n8GlobalUser['product'] = ProductService::read($item['n8_global_user']['product_id']);

        $item['n8_global_user'] = $n8GlobalUser;

        return $item;
    }
}
