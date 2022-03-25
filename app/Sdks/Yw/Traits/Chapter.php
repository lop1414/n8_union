<?php

namespace App\Sdks\Yw\Traits;


trait Chapter
{


    public function getChapterList($cpBookId){
        $uri = 'cpapi/wxNovel/getFreeChapterListByCbid';
        $param = [
            'cbid'  => $cpBookId
        ];
        return $this->apiRequest($uri,$param);
    }

}
