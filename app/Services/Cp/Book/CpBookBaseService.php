<?php

namespace App\Services\Cp\Book;


use App\Datas\BookData;
use App\Models\BookModel;
use App\Services\Cp\CpBaseService;

class CpBookBaseService extends CpBaseService
{

    public $bookModelData;

    /**
     * constructor.
     */
    public function __construct(){
        parent::__construct();

        $this->setModel(new BookModel());
        $this->bookModelData = new BookData();
    }


    public function readSave($cpBookId,$name){
        $info = $this->bookModelData->setParams(['cp_type' => $this->cpType, 'cp_book_id' => $cpBookId])->read();
        $data = [];
        if(empty($info)){
            $data = [
                'cp_type'       => $this->cpType,
                'cp_book_id'    => $cpBookId,
                'name'          => $name,
                'author_name'   => '',
                'all_words'     => 0,
                'update_time'   => null
            ];
        }else{
            //检测名称
            if($info['name'] != $name){
                $data = $info;
                $data['name'] = $name;
            }
        }

        if(!empty($data)){
            $info = $this->bookModelData->save($data)->toArray();
        }

        return $info;
    }


    public function read($cpBookId){
        $info = $this->bookModelData->setParams(['cp_type' => $this->cpType, 'cp_book_id' => $cpBookId])->read();
        if(empty($info)){
            $this->setParam('cp_book_id',$cpBookId);
            $info = $this->syncWithHook()->toArray();
        }
        return $info;
    }
}
