<?php


namespace App\Http\Controllers\Admin;

use App\Common\Enums\CpTypeEnums;
use App\Models\CpCommissionLogModel;
use App\Models\CpCommissionModel;

class CpCommissionController extends BaseController
{
    /**
     * @var string
     * 默认排序字段
     */
    protected $defaultOrderBy = 'created_at';

    /**
     * @var string
     * 默认排序类型
     */
    protected $defaultOrderType = 'asc';

    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new CpCommissionModel();

        parent::__construct();
    }




    /**
     * 更新预处理
     */
    public function updatePrepare(){
        $this->curdService->addField('commission')->addValidRule('required|int|max:100');
        $this->curdService->addField('cp_type')->addValidRule('required')
            ->addValidEnum(CpTypeEnums::class);


        $this->curdService->saveBefore(function(){
            unset($this->curdService->handleData['created_at']);
            unset($this->curdService->handleData['updated_at']);
            // 历史记录
            $cpCommissionLogModel = new CpCommissionLogModel();
            $last = $cpCommissionLogModel->where('cp_type',  $this->curdService->handleData['cp_type'])
                ->orderBy('created_at', 'desc')
                ->first();
            if(empty($last)){
                $this->curdService->handleData['created_at'] = '2000-01-01 00:00:00';
                $this->curdService->handleData['updated_at'] = '2000-01-01 00:00:00';
            }
        });

        $this->curdService->saveAfter(function (){
            //记录
            $cpCommissionLogModel = new CpCommissionLogModel();
            $cpCommissionLogModel->cp_type = $this->curdService->handleData['cp_type'];
            $cpCommissionLogModel->commission = $this->curdService->handleData['commission'];

            isset($this->curdService->handleData['created_at']) && $cpCommissionLogModel->created_at = $this->curdService->handleData['created_at'];
            isset($this->curdService->handleData['updated_at']) && $cpCommissionLogModel->updated_at = $this->curdService->handleData['updated_at'];
            $cpCommissionLogModel->save();
        });
    }
}
