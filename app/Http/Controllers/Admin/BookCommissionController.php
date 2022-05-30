<?php


namespace App\Http\Controllers\Admin;

use App\Common\Enums\CpTypeEnums;
use App\Common\Tools\CustomException;
use App\Models\BookCommissionLogModel;
use App\Models\BookCommissionModel;

class BookCommissionController extends BaseController
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
        $this->model = new BookCommissionModel();

        parent::__construct();
    }


    /**
     * 分页列表预处理
     */
    public function selectPrepare(){
        $this->curdService->selectQueryAfter(function(){
            foreach ($this->curdService->responseData['list'] as $item){
                $item->book;
            }
        });
    }


    /**
     * 创建预处理
     */
    public function createPrepare(){
        $this->curdService->addField('commission')->addValidRule('required|int|max:100');
        $this->curdService->addField('book_id')->addValidRule('required');

        // 追加主键
        $this->curdService->addColumns([$this->curdService->getModel()->getPrimaryKey()]);

        $this->curdService->saveBefore(function(){
            if($this->curdService->getModel()->uniqueExist([
                'book_id' => $this->curdService->handleData['book_id']
            ])){
                throw new CustomException([
                    'code' => 'DATA_EXIST',
                    'message' => '书籍设置已存在'
                ]);
            }
        });

        $this->curdService->saveAfter(function (){
            // 历史记录
            $bookCommissionLogModel = new BookCommissionLogModel();
            $last = $bookCommissionLogModel->where('book_id',  $this->curdService->handleData['book_id'])
                ->orderBy('created_at', 'desc')
                ->first();
            if(empty($last)){
                //额外初始记录
                $bookCommissionLogModel = new BookCommissionLogModel();
                $bookCommissionLogModel->book_id = $this->curdService->handleData['book_id'];
                $bookCommissionLogModel->commission = $this->curdService->handleData['commission'];
                $bookCommissionLogModel->created_at = '2000-01-01 00:00:00';
                $bookCommissionLogModel->updated_at = '2000-01-01 00:00:00';
                $bookCommissionLogModel->save();
            }

            $this->log($this->curdService->handleData['book_id'],$this->curdService->handleData['commission']);
        });
    }

    /**
     * 记录
     * @param $bookId
     * @param $commission
     */
    protected function log($bookId,$commission){
        $bookCommissionLogModel = new BookCommissionLogModel();
        $bookCommissionLogModel->book_id = $bookId;
        $bookCommissionLogModel->commission = $commission;
        $bookCommissionLogModel->save();
    }

    /**
     * 更新预处理
     */
    public function updatePrepare(){
        $this->curdService->addField('commission')->addValidRule('required|int|max:100');
        $this->curdService->addField('book_id')->addValidRule('required');
        $this->curdService->saveAfter(function (){
            $this->log($this->curdService->handleData['book_id'],$this->curdService->handleData['commission']);
        });
    }
}
