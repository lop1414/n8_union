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
    public function __construct(){
        $this->model = new TestBookGroupModel();

        parent::__construct();
    }


    /**
     * 分页列表预处理
     */
    public function selectPrepare(){
        $this->curdService->selectQueryAfter(function(){
            $map = $this->getAdminUserMap();

            foreach ($this->curdService->responseData['list'] as $item){
                $adminUsers = [];
                foreach ($item->admin_user_ids as $adminItem){
                    $adminUsers[] =  $adminItem->admin_id ? $map[$adminItem->admin_id]['name'] : '';
                }
                $item->admin_users = $adminUsers;
            }
        });
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
     * 分配管理员
     */
    public function assignAdminUser(Request $request){
        $this->validRule($request->post(), [
            'test_book_group_id' => 'required',
            'admin_ids' => 'required|array',
        ]);

        $testBookGroupId = $request->post('test_book_group_id');
        $adminIds = $request->post('admin_ids');

        $adminUserGroupService = new TestBookGroupService();
        $ret = $adminUserGroupService->assignAdminUser($testBookGroupId,$adminIds);
        return $this->ret($ret);
    }

}
