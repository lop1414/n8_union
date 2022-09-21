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
    static public function readChannelByCpChannelId($productId,$cpChannelId){

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
    static public function readChannelByCpChannelIdAndCheck($productId,$cpChannelId){
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


    public function sync($param = [])
    {
        $cpTypeParam = $param['cp_type'] ?? '';
        $productTypeParam = $param['product_type'] ?? '';
        if(!empty($cpTypeParam)){
            Functions::hasEnum(CpTypeEnums::class,$cpTypeParam);
        }
        if(!empty($productTypeParam)){
            Functions::hasEnum(ProductTypeEnums::class,$productTypeParam);
        }

        $container = Container::getInstance();

        $services = CpChannelService::getServices();
        foreach ($services as $service){

            $container->bind(CpChannelInterface::class,$service);
            $cpChannelService = $container->make(CpChannelService::class);

            if(!empty($productTypeParam) && $productTypeParam != $cpChannelService->getType()){
                continue;
            }

            if(!empty($cpTypeParam) && $cpTypeParam != $cpChannelService->getCpType()){
                continue;
            }


            $cpChannelService->setParam('start_date',$param['start_date']);
            $cpChannelService->setParam('end_date',$param['end_date']);

            if(!empty($param['product_ids'])){
                $cpChannelService->setParam('product_ids',$param['product_ids']);
            }

            if(!empty($param['cp_channel_id'])){
                $cpChannelService->setParam('cp_id',$param['cp_channel_id']);
            }
            $cpChannelService->sync();
        }
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
    public function isCanCreate(ProductModel $product): bool
    {

        $container = Container::getInstance();
        $services = CpChannelService::getServices();
        foreach ($services as $service){

            $container->bind(CpChannelInterface::class,$service);
            $cpChannelService = $container->make(CpChannelService::class);

            $cpType = $cpChannelService->getCpType();
            $productType = $cpChannelService->getType();

            if($product['cp_type'] == $cpType && $product['type'] == $productType){

                return $cpChannelService->isCanCreate();
            }
        }
    }
}
