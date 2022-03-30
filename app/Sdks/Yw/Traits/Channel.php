<?php

namespace App\Sdks\Yw\Traits;


trait Channel
{



    public function getChannelList($startTime,$endTime,$page = 1,$id = null,$recycle = 1,$type = 2){
        $uri = 'cpapi/wxNovel/GetQuickSpreadList';
        $param = [
            'start_time' => strtotime($startTime),
            'end_time' => strtotime($endTime),
            'recycle' => $recycle,
            'type'  => $type,
            'page'  => $page
        ];

        if($id){
            $param['channel_id'] = $id;
            unset($param['page'],$param['start_time'],$param['end_time']);
        }

        return $this->apiRequest($uri,$param);
    }


    public function createChannel($cbid,$ccid,$name,$forceChapter,$forceDesktop = 2){
        $uri = 'cpapi/WxNovel/AddQuickSpread';
        $param = [
            'cbid' => $cbid,
            'ccid' => $ccid,
            'name' => $name,
            'force_chapter'  => $forceChapter,
            'force_desktop'  => $forceDesktop
        ];

        return $this->apiRequest($uri,$param);
    }



    public function getH5ChannelList($startTime,$endTime,$page = 1, $id = null,$recycle = 1,$type = 2){
        $uri = 'cpapi/wxNovel/GetwxSpreadList';
        $param = [
            'start_time' => strtotime($startTime),
            'end_time' => strtotime($endTime),
            'recycle' => $recycle,
            'type'  => $type,
            'page'  => $page
        ];

        if($id){
            $param['channel_id'] = $id;
            $param['page'] = 1;
        }

        return $this->apiRequest($uri,$param);
    }

}
