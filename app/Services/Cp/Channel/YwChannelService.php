<?php

namespace App\Services\Cp\Channel;

use App\Common\Enums\CpTypeEnums;
use App\Common\Enums\ProductTypeEnums;
use App\Models\ChannelModel;
use App\Sdks\Yw\YwSdk;
use App\Services\Cp\Book\YwBookService;
use App\Services\Cp\Chapter\YwChapterService;


class YwChannelService extends AbstractCpChannelService
{

    protected $cpType = CpTypeEnums::YW;

    public function sync(){

        $productList = $this->getProductList();

        if($this->getParam('channel_ids')){
            $this->syncById();
            return;
        }
        foreach ($productList as $product){

            $date = $this->getParam('start_date');
            $endDate = $this->getParam('end_date');
            do{
                $startTime = $date.' 00:00:00';
                $endTime = $date.' 23:59:59';
                $sdk = $this->getSdk($product);
                $currentTotal = 0;
                $page = 1;
                do{
                    $list = [];
                    if($product['type'] == ProductTypeEnums::KYY || $product['type'] == ProductTypeEnums::APP){
                        $list  = $sdk->getChannelList($startTime,$endTime,$page);
                    }elseif ($product['type'] == ProductTypeEnums::H5){
                        $list  = $sdk->getH5ChannelList($startTime,$endTime,$page);
                    }
                    $total = $list['total_count'];
                    $currentTotal += count($list['list']);
                    foreach ($list['list'] as $item){
                        $this->saveItem($product,$item);
                    }
                    $page += 1;

                }while($currentTotal < $total);

                $date = date('Y-m-d',  strtotime('+1 day',strtotime($date)) );
            }while($date <= $endDate);

        }
    }

    /**
     * @param $product
     * @return YwSdk
     * 获取sdk
     */
    public function getSdk($product){
        $sdk = new YwSdk($product['cp_product_alias'],$product['cp_account']['account'],$product['cp_account']['cp_secret']);
        return $sdk;
    }

    /**
     * @param $product
     * @param $item
     * 保存
     */
    public function saveItem($product,$item){
        //书籍信息
        $book = (new YwBookService())->setProduct($product)->read($item['cbid']);
        //章节信息
        $chapterService = (new YwChapterService())->setProduct($product)->setBook($book);
        $chapter = $chapterService->read($item['ccid']);
        $forceChapter = $chapterService->readBySeq($item['force_chapter']);

        $extends = [];
        if($product['type'] == ProductTypeEnums::H5){
            $extends = [
                'force_subscribe' => $item['force_subscribe'],
                'url'             => $item['url'],
                'short_url'     => $item['short_url']
            ];
        }

        if($product['type'] == ProductTypeEnums::KYY){
            $extends = [
                'hap_url'   => $item['hap_url'],
                'h5_url'    => $item['h5_url'],
                'http_url'  => $item['http_url'],
                'apk_url'   => $item['apk_url']
            ];
        }


        //入库
        $this->save([
            'product_id'    => $product['id'],
            'cp_channel_id' => $item['channel_id'],
            'name'          => $item['channel_name'],
            'book_id'       => $book['id'],
            'chapter_id'    => $chapter['id'] ?? 0,
            'force_chapter_id' => $forceChapter['id'] ?? 0,
            'extends'       => $extends,
            'create_time' => date('Y-m-d H:i:s',$item['create_time']),
            'updated_time' => date('Y-m-d H:i:s',$item['create_time']),
        ]);
    }

    /**
     * 根据ID 同步
     */
    public function syncById(){

        $channelIds = $this->getParam('channel_ids');
        $channelList = (new ChannelModel())->whereIn('id',$channelIds)->get();
        foreach ($channelList as $channel){
            $startTime = date('Y-m-d H:i:s',strtotime($channel['create_time']) - 60 * 10);
            $endTime = date('Y-m-d H:i:s',strtotime($channel['create_time']) + 60 * 10);
            $product = $channel->product;
            $sdk = $this->getSdk($product);

            $list['list'] = [];
            if($product['type'] == ProductTypeEnums::KYY || $product['type'] == ProductTypeEnums::APP){
                $list  = $sdk->getChannelById($startTime,$endTime,$channel['cp_channel_id']);

            }elseif ($product['type'] == ProductTypeEnums::H5){
                $list  = $sdk->getH5ChannelById($startTime,$endTime,$channel['cp_channel_id']);
            }


            foreach ($list['list'] as $item){
                $this->saveItem($product,$item);
            }
        }

    }
}
