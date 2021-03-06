<?php

namespace App\Sdks\Yw\Traits;


trait Chapter
{


    public function getChapterList($cp_book_id){
        $uri = 'cpapi/wxNovel/getFreeChapterListByCbid';
        $param = [
            'cbid'  => $cp_book_id
        ];
        return $this->apiRequest($uri,$param);
    }

}
