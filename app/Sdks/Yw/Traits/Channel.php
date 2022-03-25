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
            $param['page'] = 1;
        }

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
