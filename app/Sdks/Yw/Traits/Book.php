<?php

namespace App\Sdks\Yw\Traits;


trait Book
{
    /**
     * 获取书籍id
     * @return false|string[]
     */
    public function getBookIds(){
        $bookIdFile = $this->getSdkPath('Data/'.$this->appflags.'/cp_book_ids.csv');
        if(!file_exists($bookIdFile)){
            return [];
        }
        $tmp = file_get_contents($bookIdFile);
        return explode("\r\n",trim($tmp));
    }


    public function getBookInfo($cp_book_id){
        $uri = 'cpapi/wxNovel/getNovelInfoByCbid';
        $param = [
            'cbid'  => $cp_book_id
        ];
        return $this->apiRequest($uri,$param);
    }

}
