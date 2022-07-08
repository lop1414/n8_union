<?php
namespace App\Http\Controllers\Admin;


use App\Common\Enums\StatusEnum;
use App\Models\BookLabelModel;
use App\Services\BookLabelService;
use Illuminate\Http\Request;

class BookLabelController extends BaseController
{

    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new BookLabelModel();

        parent::__construct();
    }

    /**
     * 分页列表预处理
     */
    public function selectPrepare(){
        $this->curdService->selectQueryBefore(function (){
            $this->dataFilter();
        });

        $this->curdService->selectQueryAfter(function(){
            foreach ($this->curdService->responseData['list'] as $item){
                $item->books;
            }
        });
    }

    /**
     * 过滤
     */
    public function dataFilter(){

        $this->curdService->customBuilder(function ($builder){
            $req = $this->curdService->requestData;

            $keyword = $req['keyword'] ?? '';
            if(!empty($keyword)){
                $builder->whereRaw(" (`name` LIKE '%{$keyword}%' OR `id` LIKE '%{$keyword}%' OR `cp_book_id` LIKE '%{$keyword}%')");
            }
        });
    }

    /**
     * 列表预处理
     */
    public function getPrepare(){
        $this->curdService->getQueryBefore(function (){
            $this->dataFilter();
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
     * 分配书籍
     */
    public function assignBook(Request $request){
        $this->validRule($request->post(), [
            'book_label_id' => 'required',
            'book_ids' => 'required|array',
        ]);

        $bookLabelId = $request->post('book_label_id');
        $bookIds = $request->post('book_ids');

        $bookLabelService = new BookLabelService();
        $ret = $bookLabelService->assignBook($bookLabelId,$bookIds);
        return $this->ret($ret);
    }

}
