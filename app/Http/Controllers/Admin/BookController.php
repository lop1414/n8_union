<?php
namespace App\Http\Controllers\Admin;


use App\Common\Tools\CustomException;
use App\Models\BookModel;
use App\Models\ProductModel;
use App\Services\BookService;

class BookController extends BaseController
{

    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new BookModel();

        parent::__construct();
    }

    /**
     * 过滤
     */
    protected function dataFilter(){

        $this->curdService->customBuilder(function ($builder){
            $req = $this->curdService->requestData;

            $keyword = $req['keyword'] ?? '';
            if(!empty($keyword)){
                $builder->whereRaw(" (`name` LIKE '%{$keyword}%' OR `id` LIKE '%{$keyword}%' OR `cp_book_id` LIKE '%{$keyword}%')");
            }
        });
    }

    protected function syncBook(){
        $this->curdService->customBuilder(function ($builder){
            $req = $this->curdService->requestData;
            $keyword = intval($req['keyword'] ?? '');

            if(isset($req['has_sync']) && $req['has_sync'] == 1 && !empty($keyword)){
                if(!isset($req['product_id'])){
                    throw new CustomException([
                        'code' => 'NOT_EXISTENT',
                        'message' => 'product_id 不能为空'
                    ]);
                }
                $product = ProductModel::find($req['product_id']);
                (new BookService())->sync($product->cp_type, [$product->id] , $keyword);

            }
        });
    }

    /**
     * 分页列表预处理
     */
    public function selectPrepare(){
        $this->curdService->selectQueryBefore(function (){
            $this->dataFilter();
            $this->syncBook();
        });
    }

    /**
     * 列表预处理
     */
    public function getPrepare(){
        $this->curdService->getQueryBefore(function (){
            $this->dataFilter();
            $this->syncBook();
        });
    }

}
