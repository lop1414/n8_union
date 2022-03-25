<?php

namespace App\Services;


use App\Common\Services\BaseService;
use App\Datas\ChapterData;

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
        $info = $this->modelData
            ->setParams(['book_id' => $bookId,'cp_chapter_id' => $cpChapterId])
            ->read();

        return $info;
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
        $info = $this->modelData
            ->setParams(['book_id' => $bookId,'seq' => $seq])
            ->read();
        return $info;
    }


}
