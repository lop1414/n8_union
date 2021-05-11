<?php

namespace App\Http\Controllers\Front;

use App\Common\Controllers\Front\FrontController;
use App\Datas\ChapterData;
use Illuminate\Http\Request;

class ChapterController extends FrontController
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
            'book_id'       =>  'required',
            'cp_chapter_id' =>  'required',
        ]);

        $chapterData = new ChapterData();
        $chapter = $chapterData->save($req);

        return $this->success($chapter);
    }
}
