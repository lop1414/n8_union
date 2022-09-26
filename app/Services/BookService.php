<?php

namespace App\Services;


use App\Common\Enums\CpTypeEnums;
use App\Common\Helpers\Functions;
use App\Common\Services\BaseService;
use App\Datas\BookData;
use App\Services\Cp\Book\CpBookInterface;
use App\Services\Cp\CpBookService;
use Illuminate\Container\Container;

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
        return $this->modelData->setParams(['id' => $bookId])->read();
    }


    public function sync($cpType, $productIds, $cpBookId )
    {
        $data = $this->readByApi($cpType, $productIds, $cpBookId);

        $bookModelData = new BookData();
        foreach ($data as $item){
            $bookModelData->save($item);
        }
    }


    public function readByApi($cpType, $productIds = [], $cpBookId = 0): array
    {

        if(!empty($cpType)){
            Functions::hasEnum(CpTypeEnums::class,$cpType);
        }

        $container = Container::getInstance();
        $services = CpBookService::getServices();

        $data = [];
        foreach ($services as $service){

            $container->bind(CpBookInterface::class,$service);
            $cpBookService = $container->make(CpBookService::class);

            if(!empty($cpType) && $cpType != $cpBookService->getCpType()){
                continue;
            }

            if(!empty($productIds)){
                $cpBookService->setParam('product_ids',$productIds);
            }

            if(!empty($cpBookId)){
                $cpBookService->setParam('cp_id',$cpBookId);
            }
            $data[] =  $cpBookService->readByApi();
        }
        return $data;
    }



}
