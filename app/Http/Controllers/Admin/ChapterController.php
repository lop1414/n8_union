<?php
namespace App\Http\Controllers\Admin;


use App\Common\Enums\CpTypeEnums;
use App\Common\Tools\CustomException;
use App\Common\Tools\CustomLock;
use App\Models\ChapterModel;
use App\Models\ProductModel;
use App\Services\ChapterService;

class ChapterController extends BaseController
{

    /**
     * @var string
     * 默认排序字段
     */
    protected $defaultOrderBy = 'seq';

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
        $this->model = new ChapterModel();

        parent::__construct();
    }

    /**
     * 分页列表预处理
     */
    public function selectPrepare(){
        $this->curdService->selectQueryBefore(function (){
            $this->dataFilter();
            $this->syncChapter();
        });
        $this->curdService->selectQueryAfter(function (){
            $req = $this->curdService->requestData;
            $product = ProductModel::find($req['product_id']);
            if($product['cp_type'] == CpTypeEnums::FQ){
                // 兼容FQ没有章节接口
                $maxSeq = 8;
                $data = [];
                for ($i=1;$i<= $maxSeq;$i++){
                    $data[] = [
                        'id'  => $i,
                        'seq' => $i,
                        'name' => "第{$i}章"
                    ];
                }
                $this->curdService->responseData['list'] = $data;
            }
        });
    }

    /**
     * 过滤
     */
    protected function dataFilter(){
        $this->curdService->addField('book_id')->addValidRule('required');

        $this->curdService->customBuilder(function ($builder){
            $req = $this->curdService->requestData;
            $builder->where('book_id',$req['book_id']);

            $keyword = $req['keyword'] ?? '';
            if(!empty($keyword)){
                $builder->whereRaw(" (`name` LIKE '%{$keyword}%' OR `id` LIKE '%{$keyword}%' OR `cp_chapter_id` LIKE '%{$keyword}%')");
            }
        });
    }

    protected function syncChapter(){
        $this->curdService->customBuilder(function ($builder){
            $req = $this->curdService->requestData;

            if(isset($req['is_sync']) && $req['is_sync'] == 1){
                if(!isset($req['product_id'])){
                    throw new CustomException([
                        'code' => 'NOT_EXISTENT',
                        'message' => 'product_id 不能为空'
                    ]);
                }

                //同步频率限制
                $key = "admin_sync_chapter_frequency_limit|{$req['product_id']}|{$req['book_id']}";
                $lock = new CustomLock($key);

                if(!$lock->isLock()){
                    $lock->set(60*5);
                    $product = ProductModel::find($req['product_id']);
                    (new ChapterService())->sync($product->cp_type, [$product->id] , $req['book_id']);
                }
            }
        });
    }

    /**
     * 列表预处理
     */
    public function getPrepare(){
        $this->curdService->getQueryBefore(function (){
            $this->dataFilter();
            $this->syncChapter();
        });
    }

}
