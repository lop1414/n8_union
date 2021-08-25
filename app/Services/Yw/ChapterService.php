<?php

namespace App\Services\Yw;


use App\Datas\ChapterData;
use App\Models\ChapterModel;
use App\Sdks\Yw\YwSdk;

class ChapterService extends YwService
{


    protected $book;

    public $chapterModelData;

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


    public function readSave($cpChapterId,$cpChapterName,$cpChapterIndex){
        $info = $this->chapterModelData->setParams(['book_id'=>$this->book['id'],'cp_chapter_id' => $cpChapterId])->read();
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


    public function read($cpChapterId){
        $query = $this->model->where('book_id',$this->book['id'])->where('cp_chapter_id',$cpChapterId);
        $info = $query->first();
        if(empty($info)){
            $this->sync();
            $info = $query->first();
        }
        return $info;
    }



    public function readBySeq($seq){
        $query = $this->model->where('book_id',$this->book['id'])->where('seq',$seq);
        $info = $query->first();
        if(empty($info)){
            $this->sync();
            $info = $query->first();
        }
        return $info;
    }



    public function sync(){


        $sdk = new YwSdk($this->product['cp_product_alias'],$this->product['cp_account']['account'],$this->product['cp_account']['cp_secret']);

        $list = $sdk->getChapterList( $this->book['cp_book_id']);
        $list = $list['chapter_list'] ?? [];

        foreach ($list as $chapter){
            $this->chapterModelData->save([
                'book_id'       => $this->book['id'],
                'cp_chapter_id' => $chapter['ccid'],
                'name'          => $chapter['chapter_title'],
                'seq'           => $chapter['chapter_seq']
            ]);
        }
    }
}
