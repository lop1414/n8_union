<?php

namespace App\Services;

use App\Common\Services\BaseService;
use App\Datas\UserBookReadData;
use App\Models\UserBookReadModel;

class UserBookReadService extends BaseService
{

    protected $statUserReadModelData;

    public function __construct(){
        parent::__construct();
        $this->model = new UserBookReadModel();
        $this->statUserReadModelData = new UserBookReadData();
    }

    /**
     * @param $readInfo
     * @return bool
     * 分析阅读记录
     */
    public function analysis($readInfo){
        $info = $this->read($readInfo['n8_guid'],$readInfo['book_id']);
        // 创建
        if(empty($info)){
            $this->create([
                'n8_guid' => $readInfo['n8_guid'],
                'book_id' => $readInfo['book_id'],
                'last_chapter_id' => $readInfo['chapter_id'],
                'start_time' => $readInfo['action_time'],
                'last_time' => $readInfo['action_time']
            ]);
            return true;
        }

        // 无需更新
        if($info['start_time'] == $readInfo['action_time'] && $info['last_chapter_id'] == $readInfo['chapter_id']){
            return true;
        }

        // 更新开始时间
        if($info['start_time'] > $readInfo['action_time']){
            $this->update($info['id'],['start_time' => $readInfo['action_time']]);
            return true;
        }


        $this->update($info['id'],[
            'last_chapter_id' => $readInfo['chapter_id'],
            'last_time' => $readInfo['action_time']
        ]);


        return true;
    }

    public function read($n8Guid,$bookId){
        return $this->statUserReadModelData->setParams(['n8_guid'=>$n8Guid,'book_id' => $bookId])->read();
    }

    public function create($data){
        $info  = new UserBookReadModel();
        $info->n8_guid = $data['n8_guid'];
        $info->book_id = $data['book_id'];
        $info->last_chapter_id = $data['last_chapter_id'];
        $info->start_time = $data['start_time'];
        $info->last_time = $data['last_time'];
        $info->save();
    }

    public function update($id,$data){
        $this->model
            ->where('id',$id)
            ->update($data);

        // 删除缓存
        $this->statUserReadModelData->setParams(['id' => $id])->clear();
    }
}
