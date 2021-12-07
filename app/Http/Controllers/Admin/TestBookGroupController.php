<?php
namespace App\Http\Controllers\Admin;


use App\Common\Enums\StatusEnum;
use App\Models\TestBookGroupModel;

class TestBookGroupController extends BaseController
{

    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new TestBookGroupModel();

        parent::__construct();
    }

    /**
     * 创建预处理
     */
    public function createPrepare(){
        $this->saveValidRule();
    }

    /**
     * 保持验证规则
     */
    public function saveValidRule(){
        $this->curdService->addField('name')->addValidRule('required');
        $this->curdService->addField('status')
            ->addValidEnum(StatusEnum::class)
            ->addDefaultValue(StatusEnum::ENABLE);

    }

    /**
     * 更新预处理
     */
    public function updatePrepare(){
        $this->saveValidRule();
    }

}
