<?php

namespace App\Services;


use App\Common\Enums\CpTypeEnums;
use App\Common\Helpers\Functions;
use App\Common\Services\BaseService;
use App\Datas\ChapterData;
use App\Services\Cp\Chapter\CpChapterInterface;
use App\Services\Cp\CpChapterService;
use Illuminate\Container\Container;

class ChapterService extends BaseService
{
    private $modelData;

    public function __construct(){
        parent::__construct();
        $this->modelData = new ChapterData();
    }

    /**
     * @param array $data
     * @return array
     * 更新保存
     */
    public function save(array $data): array
    {
        $info = $this->modelData->save($data);
        return $info->toArray();
    }

    /**
     * @param $bookId
     * @param $cpChapterId
     * @param $cpChapterName
     * @param $seq
     * @return array
     * @throws \App\Common\Tools\CustomException
     * 获取详情没有则保存
     */
    public function readSave($bookId,$cpChapterId,$cpChapterName,$seq): array
    {

        $info = [];
        if(!empty($cpChapterId)){

            $info = $this->readByUniqueKey($bookId,$cpChapterId);
        }elseif (!empty($seq)){
            $info = $this->readBySeq($bookId,$seq);
        }

        if(empty($info) || $info['seq'] != $seq){
            $info = $this->modelData->save([
                'book_id'       => $bookId,
                'cp_chapter_id' => $cpChapterId,
                'name'          => $cpChapterName,
                'seq'           => $seq
            ])->toArray();
        }
        return $info;
    }

    /**
     * @param $bookId
     * @param $cpChapterId
     * @return array|null
     * @throws \App\Common\Tools\CustomException
     * 获取详情
     */
    public function readByUniqueKey($bookId,$cpChapterId): ?array
    {
        return $this->modelData
            ->setParams(['book_id' => $bookId,'cp_chapter_id' => $cpChapterId])
            ->read();
    }

    /**
     * @param $bookId
     * @param $seq
     * @return array|null
     * @throws \App\Common\Tools\CustomException
     * 根据章节获取详情
     */
    public function readBySeq($bookId,$seq): ?array
    {
        return $this->modelData
            ->setParams(['book_id' => $bookId,'seq' => $seq])
            ->read();
    }



    public function sync($cpType, $productIds, $cpBookId )
    {
        $data = $this->getByApi($cpType, $productIds, $cpBookId);

        $chapterModelData = new ChapterData();
        foreach ($data as $item){
            $chapterModelData->save($item);
        }
    }


    public function getByApi($cpType, $productIds = [], $bookId = 0): array
    {

        if(!empty($cpType)){
            Functions::hasEnum(CpTypeEnums::class,$cpType);
        }

        $container = Container::getInstance();
        $services = CpChapterService::getServices();

        $data = [];
        foreach ($services as $service){

            $container->bind(CpChapterInterface::class,$service);
            $cpBookService = $container->make(CpChapterService::class);

            if(!empty($cpType) && $cpType != $cpBookService->getCpType()){
                continue;
            }

            if(!empty($productIds)){
                $cpBookService->setParam('product_ids',$productIds);
            }

            if(!empty($bookId)){
                $cpBookService->setParam('book_id',$bookId);
            }
            $data =  array_merge($cpBookService->getByApi(),$data);
        }
        return $data;
    }


}
