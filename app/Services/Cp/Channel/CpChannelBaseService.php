<?php

namespace App\Services\Cp\Channel;


use App\Models\ChannelModel;
use App\Services\Cp\CpBaseService;

class CpChannelBaseService extends CpBaseService
{
    /**
     * constructor.
     */
    public function __construct(){
        parent::__construct();

        $this->setModel(new ChannelModel());
    }




    public function save($data){
        $info = (new ChannelModel())
            ->where('product_id',$data['product_id'])
            ->where('cp_channel_id',$data['cp_channel_id'])
            ->first();

        if(empty($info)){
            $info = new ChannelModel();
        }

        $info->product_id = $data['product_id'];
        $info->cp_channel_id = $data['cp_channel_id'];
        $info->name = $data['name'];
        $info->book_id = $data['book_id'];
        $info->chapter_id = $data['chapter_id'];
        $info->force_chapter_id = $data['force_chapter_id'];
        $info->extends = $data['extends'];
        $info->create_time = $data['create_time'];
        $info->updated_time = $data['create_time'];
        $info->save();
        return $info;
    }
}
