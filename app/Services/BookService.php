<?php

namespace App\Services;


use App\Common\Services\BaseService;
use App\Datas\BookData;

class BookService extends BaseService
{
    private $modelData;

    public function __construct()
    {
        parent::__construct();
        $this->modelData = new BookData();
    }

    /**
     * @param array $data
     * @return array
     * @throws \App\Common\Tools\CustomException
     * 获取详情 没有则保存
     */
    public function readSave(array $data): array
    {
        $info = $this->readByUniqueKey($data['cp_book_id'],$data['cp_type']);
        $saveData = [];

        if(empty($info)){
            $saveData = $data;

        }elseif($info['name'] != $data['name']){
            //更改名称
            $saveData = $info;
            $saveData['name'] = $data['name'];
        }

        if(!empty($saveData)){
            $info = $this->save($saveData);
        }

        return $info;
    }

    /**
     * @param string $cpBookId
     * @param string $cpType
     * @return array|null
     * @throws \App\Common\Tools\CustomException
     * 获取详情
     */
    public function readByUniqueKey(string $cpBookId,string $cpType): ?array
    {
        $info = $this->modelData->setParams(['cp_type' => $cpType, 'cp_book_id' => $cpBookId])->read();
        return $info;
    }

    /**
     * @param array $data
     * @return array
     * 更新保存
     */
    public function save(array $data): array
    {
        $info = $this->modelData->save($data);
        return $info->toArray();
    }

    /**
     * @param int $bookId
     * @return array|null
     * @throws \App\Common\Tools\CustomException
     * 获取详情
     */
    public function read(int $bookId): ?array
    {
        $info = $this->modelData->setParams(['id' => $bookId])->read();
        return $info;
    }



}
