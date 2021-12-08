<?php
namespace App\Http\Controllers\Admin;


use App\Common\Enums\StatusEnum;
use App\Models\TestBookModel;
use App\Services\TestBookGroupService;
use Illuminate\Http\Request;

class TestBookController extends BaseController
{

    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new TestBookModel();

        parent::__construct();
    }


    /**
     * 分页列表预处理
     */
    public function selectPrepare(){
        $this->curdService->addField('product_id')->addValidRule('required');
        $this->curdService->selectQueryBefore(function(){
            $this->curdService->customBuilder(function ($builder){
                $builder->where('product_id',$this->curdService->requestData['product_id']);
            });
        });
        $this->curdService->selectQueryAfter(function(){

            foreach ($this->curdService->responseData['list'] as $item){
                $item->book;
                $item->test_book_groups;
            }
        });
    }


    /**
     * 详情预处理
     */
    public function readPrepare(){

        $this->curdService->findAfter(function(){
            $this->curdService->responseData->book;
        });
    }


    /**
     * 保持验证规则
     */
    public function saveValidRule(){
        $this->curdService->addField('product_id')->addValidRule('required');
        $this->curdService->addField('book_id')->addValidRule('required');
        $this->curdService->addField('start_at')->addValidRule('required');
        $this->curdService->addField('end_at')->addValidRule('required');
        $this->curdService->addField('status')
            ->addValidEnum(StatusEnum::class)
            ->addDefaultValue(StatusEnum::ENABLE);

    }

    /**
     * 创建预处理
     */
    public function createPrepare(){
        $this->saveValidRule();
        $this->curdService->saveBefore(function(){
            $this->curdService->handleData['start_at'] = date('Y-m-d H:00:00',strtotime($this->curdService->handleData['start_at']));
            $this->curdService->handleData['end_at'] = date('Y-m-d H:00:00',strtotime($this->curdService->handleData['end_at']));
        });
    }



    /**
     * 更新预处理
     */
    public function updatePrepare(){
        $this->saveValidRule();
        $this->curdService->saveBefore(function(){
            $this->curdService->handleData['start_at'] = date('Y-m-d H:00:00',strtotime($this->curdService->handleData['start_at']));
            $this->curdService->handleData['end_at'] = date('Y-m-d H:00:00',strtotime($this->curdService->handleData['end_at']));
        });
    }



    /**
     * @param Request $request
     * @return mixed
     * @throws \App\Common\Tools\CustomException
     * 分配测试书籍组
     */
    public function assignTestBookGroup(Request $request){
        $this->validRule($request->post(), [
            'test_book_id' => 'required',
            'test_book_group_ids' => 'required|array',
        ]);

        $testBookGroupIds = $request->post('test_book_group_ids');
        $testBookId = $request->post('test_book_id');

        $adminUserGroupService = new TestBookGroupService();
        $ret = $adminUserGroupService->assignTestBook([$testBookId],$testBookGroupIds);
        return $this->ret($ret);
    }

}
