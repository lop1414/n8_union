<?php

namespace App\Services\Qy;


use App\Common\Enums\CpTypeEnums;
use App\Datas\BookData;
use App\Models\BookModel;

class BookService extends QyService
{

    /**
     * constructor.
     */
    public function __construct(){
        parent::__construct();

        $this->setModel(new BookModel());
    }




    public function read($cpBookId,$name){
        $info = $this->model
            ->where('cp_type',CpTypeEnums::QY)
            ->where('cp_book_id',$cpBookId)
            ->first();
        if(empty($info)){
            $info = $this->created($cpBookId,$name);
        }
        return $info;
    }


    /**
     * @param $cpBookId
     * @param $name
     * @return mixed
     */
    public function created($cpBookId,$name){

        $bookData = new BookData();

        return $bookData->save([
            'cp_type'       => $this->product['cp_type'],
            'cp_book_id'    => $cpBookId,
            'name'          => $name,
            'author_name'   => '',
            'all_words'     => 0,
            'update_time'   => null
        ]);
    }

}
