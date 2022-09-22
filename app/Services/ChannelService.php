<?php

namespace App\Services;

use App\Common\Enums\CpTypeEnums;
use App\Common\Enums\ProductTypeEnums;
use App\Common\Helpers\Functions;
use App\Common\Services\BaseService;
use App\Common\Tools\CustomException;
use App\Datas\ChannelData;
use App\Datas\ChannelExtendData;
use App\Models\BookModel;
use App\Models\ChapterModel;
use App\Models\ProductModel;
use App\Services\Cp\Channel\CpChannelInterface;
use App\Services\Cp\CpChannelService;
use Illuminate\Container\Container;

class ChannelService extends BaseService
{


    /**
     * @param $productId
     * @param $cpChannelId
     * @return array|null
     * @throws CustomException
     * 通过cpChannelId,productId 读取渠道信息
     */
    static public function readChannelByCpChannelId($productId,$cpChannelId): ?array
    {

        if(empty($cpChannelId)){
            return [];
        }

        $channel = (new ChannelData())
            ->setParams(['product_id'=> $productId, 'cp_channel_id'=>$cpChannelId])
            ->read();

        $channelExtend = (new ChannelExtendData())->setParams(['channel_id' => $channel['id']])->read();

        $channel['channel_extend'] = $channelExtend;

        return $channel;
    }


    /**
     * @param $productId
     * @param $cpChannelId
     * @return array
     * @throws CustomException
     * 通过cpChannelId,productId 读取渠道信息并检查
     */
    static public function readChannelByCpChannelIdAndCheck($productId,$cpChannelId): array
    {
        $channel = self::readChannelByCpChannelId($productId,$cpChannelId);
        if(empty($channel)){
            throw new CustomException([
                'code'       => 'NO_CHANNEL',
                'message'    => "找不到渠道（产品ID:{$productId},N8CP渠道ID:{$cpChannelId}）",
                '#admin_id#' => 0
            ]);
        }

        if(empty($channel['channel_extend'])){

            throw new CustomException([
                'code'       => 'NO_CHANNEL_EXTEND',
                'message'    => "渠道待认领（产品ID:{$productId},N8CP渠道ID:{$cpChannelId}）",
                '#admin_id#' => 0
            ]);
        }
        return $channel;
    }


    public function sync($cpType, $productType, $startDate, $endDate, $productIds = [] , $cpChannelId = 0)
    {
        $data = $this->getByApi($cpType, $productType, $startDate, $endDate, $productIds, $cpChannelId);

        $channelModelData = new ChannelData();
        foreach ($data as $item){
            $channelModelData->save($item);
        }
    }


    public function getByApi($cpType, $productType, $startDate, $endDate, $productIds = [], $cpChannelId = 0): array
    {

        if(!empty($cpType)){
            Functions::hasEnum(CpTypeEnums::class,$cpType);
        }
        if(!empty($productType)){
            Functions::hasEnum(ProductTypeEnums::class,$productType);
        }

        $container = Container::getInstance();
        $services = CpChannelService::getServices();

        $data = [];
        foreach ($services as $service){

            $container->bind(CpChannelInterface::class,$service);
            $cpChannelService = $container->make(CpChannelService::class);

            if(!empty($productType) && $productType != $cpChannelService->getType()){
                continue;
            }

            if(!empty($cpType) && $cpType != $cpChannelService->getCpType()){
                continue;
            }

            $cpChannelService->setParam('start_date',$startDate);
            $cpChannelService->setParam('end_date',$endDate);

            if(!empty($productIds)){
                $cpChannelService->setParam('product_ids',$productIds);
            }

            if(!empty($cpChannelId)){
                $cpChannelService->setParam('cp_id',$cpChannelId);
            }
            $items = $cpChannelService->getByApi();
            $data = array_merge($data,$items);
        }
        return $data;
    }

    public function create(ProductModel $product,string $name,BookModel $book,ChapterModel $chapter,?ChapterModel $cpForceChapter): string
    {

        $container = Container::getInstance();
        $services = CpChannelService::getServices();
        foreach ($services as $service){

            $container->bind(CpChannelInterface::class,$service);
            $cpChannelService = $container->make(CpChannelService::class);

            $cpType = $cpChannelService->getCpType();
            $productType = $cpChannelService->getType();

            if($product['cp_type'] == $cpType && $product['type'] == $productType){

                if(!method_exists($cpChannelService,'create')){
                    return false;
                }

                $cpChannelService->setParam('product_id',$product['id']);

                return $cpChannelService->create($name,$book,$chapter,$cpForceChapter);
            }
        }
    }


    /**
     * 是否可以创建渠道
     * @param ProductModel $product
     * @return bool
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function isCanApiCreate(ProductModel $product): bool
    {

        $container = Container::getInstance();
        $services = CpChannelService::getServices();
        foreach ($services as $service){

            $container->bind(CpChannelInterface::class,$service);
            $cpChannelService = $container->make(CpChannelService::class);

            $cpType = $cpChannelService->getCpType();
            $productType = $cpChannelService->getType();

            if($product['cp_type'] == $cpType && $product['type'] == $productType){

                return $cpChannelService->isCanApiCreate();
            }
        }
        return false;
    }


    /**
     * 是否可以查询
     * @param ProductModel $product
     * @return bool
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function isCanApiSelect(ProductModel $product): bool
    {

        $container = Container::getInstance();
        $services = CpChannelService::getServices();
        foreach ($services as $service){

            $container->bind(CpChannelInterface::class,$service);
            $cpChannelService = $container->make(CpChannelService::class);

            $cpType = $cpChannelService->getCpType();
            $productType = $cpChannelService->getType();

            if($product['cp_type'] == $cpType && $product['type'] == $productType){
                return true;
            }
        }

        return false;
    }
}
