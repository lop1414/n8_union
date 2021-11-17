<?php

namespace App\Services\Cp\Chapter;

use App\Common\Tools\CustomException;
use App\Datas\ChapterData;
use App\Models\ChapterModel;
use App\Services\Cp\CpBaseService;

class CpChapterBaseService extends CpBaseService
{

    public $chapterModelData;
    protected $book;

    /**
     * constructor.
     */
    public function __construct(){
        parent::__construct();

        $this->setModel(new ChapterModel());
        $this->chapterModelData = new ChapterData();
    }


    public function setBook($info){
        $this->book = $info;
        return $this;
    }


    /**
     * @param $cpChapterId
     * @param $cpChapterName
     * @param $cpChapterIndex
     * @return mixed|null
     * @throws CustomException
     * read 或 save
     */
    public function readSave($cpChapterId,$cpChapterName,$cpChapterIndex){
        $info = $this->chapterModelData
            ->setParams(['book_id'=>$this->book['id'],'cp_chapter_id' => $cpChapterId])
            ->read();
        if(empty($info)){
            $info = $this->chapterModelData->save([
                'book_id'       => $this->book['id'],
                'cp_chapter_id' => $cpChapterId,
                'name'          => $cpChapterName,
                'seq'           => $cpChapterIndex
            ])->toArray();
        }
        return $info;
    }



//    public function readSaveBySeq($seq,$cpChapterId,$name){
//        $info = $this->model
//            ->where('book_id',$this->book['id'])
//            ->where('seq',$seq)
//            ->first();
//        if(empty($info)){
//            $info = $this->chapterModelData->save([
//                'book_id'       => $this->book['id'],
//                'cp_chapter_id' => $cpChapterId,
//                'name'          => $name,
//                'seq'           => $seq
//            ])->toArray();
//        }
//        return $info;
//    }


    public function read($cpChapterId){
        $info = $this->chapterModelData
            ->setParams(['book_id'=>$this->book['id'],'cp_chapter_id' => $cpChapterId])
            ->read();
        if(empty($info)){
            $this->setParam('cp_book_id',$this->book['cp_book_id']);
            $this->setParam('cp_chapter_id',$cpChapterId);
            $info = $this->syncWithHook();
            if(!empty($info)){
                $info = $info->toArray();
            }
        }
        return $info;
    }


    public function readBySeq($seq){
        $query = $this->model->where('book_id',$this->book['id'])->where('seq',$seq);
        $info = $query->first();
        if(empty($info)){
            $this->setParam('cp_book_id',$this->book['cp_book_id']);
            $this->syncWithHook();
            $info = $query->first();
            if(!empty($info)){
                $info = $info->toArray();
            }
        }
        return $info;
    }
}
