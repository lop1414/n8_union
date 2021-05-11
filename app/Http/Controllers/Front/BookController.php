<?php

namespace App\Http\Controllers\Front;

use App\Common\Controllers\Front\FrontController;
use App\Datas\BookData;
use Illuminate\Http\Request;

class BookController extends FrontController
{
    /**
     * constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }



    public function create(Request $request){
        $req = $request->all();
        $this->validRule($req,[
            'cp_type'       =>  'required',
            'cp_book_id'    =>  'required',
        ]);

        $bookData = new BookData();
        $book = $bookData->save($req);

        return $this->success($book);
    }
}
