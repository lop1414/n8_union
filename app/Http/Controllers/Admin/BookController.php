<?php
namespace App\Http\Controllers\Admin;


use App\Common\Tools\CustomException;
use App\Common\Tools\CustomLock;
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
            $cpBookId =  $req['cp_book_id'] ?? '';
            if(!empty($cpBookId)){
                $builder->where('cp_book_id',$cpBookId);
            }
            if(isset($req['product_id'])){
                $product = ProductModel::find($req['product_id']);
                $builder->where('cp_type',$product['cp_type']);
            }
        });
    }

    protected function syncBook(){
        $this->curdService->customBuilder(function ($builder){
            $req = $this->curdService->requestData;
            $cpBookId = intval($req['cp_book_id'] ?? '');

            if(isset($req['is_sync']) && $req['is_sync'] == 1 && !empty($cpBookId)){
                if(!isset($req['product_id'])){
                    throw new CustomException([
                        'code' => 'NOT_EXISTENT',
                        'message' => 'product_id 不能为空'
                    ]);
                }

                //同步频率限制
                $key = "admin_sync_book_frequency_limit|{$req['product_id']}|{$cpBookId}";
                $lock = new CustomLock($key);
                if(!$lock->isLock()) {
                    $lock->set(60*5);
                    $product = ProductModel::find($req['product_id']);
                    (new BookService())->sync($product->cp_type, [$product->id], $cpBookId);
                }
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
