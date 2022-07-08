<?php

namespace App\Services;

use App\Common\Services\BaseService;
use App\Common\Tools\CustomException;
use App\Models\BookBookLabelModel;
use App\Models\BookLabelModel;

class BookLabelService extends BaseService
{
    /**
     * constructor.
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * @param int $bookLabelId
     * @param array $bookIds
     * @return bool
     * @throws CustomException
     * 分配书籍
     */
    public function assignBook(int $bookLabelId,array $bookIds): bool
    {

        $this->check($bookLabelId);
//        //删除
        (new BookBookLabelModel())->where('book_label_id',$bookLabelId)->delete();

        foreach ($bookIds as $bookId){
            $info = new BookBookLabelModel();
            $info->book_id = $bookId;
            $info->book_label_id = $bookLabelId;
            $info->save();
        }
        return true;
    }

    public function check($bookLabelId){
        $bookLabel = BookLabelModel::find($bookLabelId);
        if(empty($bookLabel)){
            throw new CustomException([
                'code' => 'NOT_FOUND_BOOK_Label',
                'message' => '找不到该书籍标签',
            ]);
        }
    }


}
