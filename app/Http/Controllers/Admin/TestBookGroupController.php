<?php
namespace App\Http\Controllers\Admin;


use App\Common\Enums\StatusEnum;
use App\Models\TestBookGroupModel;
use App\Services\TestBookGroupService;
use Illuminate\Http\Request;

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


    /**
     * @param Request $request
     * @return mixed
     * @throws \App\Common\Tools\CustomException
     * 分配
     */
    public function assign(Request $request){
        $this->validRule($request->post(), [
            'test_book_group_id' => 'required',
            'test_book_ids' => 'required|array',
        ]);

        $testBookGroupId = $request->post('test_book_group_id');
        $testBookIds = $request->post('test_book_ids');

        $adminUserGroupService = new TestBookGroupService();
        $ret = $adminUserGroupService->batchUpdate($testBookGroupId, $testBookIds);
        return $this->ret($ret);
    }

}
