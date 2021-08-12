<?php

namespace App\Services\Qy;


use App\Datas\ChapterData;
use App\Models\ChapterModel;


class ChapterService extends QyService
{


    protected $book;

    /**
     * constructor.
     */
    public function __construct(){
        parent::__construct();

        $this->setModel(new ChapterModel());
    }


    public function setBook($info){
        $this->book = $info;
        return $this;
    }


    public function read($cpChapterId,$name,$seq){
        $query = $this->model->where('book_id',$this->book['id'])->where('cp_chapter_id',$cpChapterId);
        $info = $query->first();
        if(empty($info)){
            $this->created($cpChapterId,$name,$seq);
            $info = $query->first();
        }
        return $info;
    }




    public function created($cpChapterId,$name,$seq){

        $chapterData = new ChapterData();

        $chapterData->save([
            'book_id'       => $this->book['id'],
            'cp_chapter_id' => $cpChapterId,
            'name'          => $name,
            'seq'           => $seq
        ]);
    }



    public function readBySeq($seq,$cpChapterId,$name){
        $query = $this->model->where('book_id',$this->book['id'])->where('seq',$seq);
        $info = $query->first();
        if(empty($info)){
            $this->created($cpChapterId,$name,$seq);
            $info = $query->first();
        }
        return $info;
    }
}
