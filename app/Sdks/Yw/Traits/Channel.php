<?php

namespace App\Sdks\Yw\Traits;


trait Channel
{



    public function getChannelList($startTime,$endTime,$page = 1, $recycle = 1,$type = 2){
        $uri = 'cpapi/wxNovel/GetQuickSpreadList';
        $param = [
            'start_time' => strtotime($startTime),
            'end_time' => strtotime($endTime),
            'recycle' => $recycle,
            'type'  => $type,
            'page'  => $page
        ];

        return $this->apiRequest($uri,$param);
    }

}
