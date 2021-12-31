<?php

namespace App\Services\Cp\Channel;

use App\Common\Enums\CpTypeEnums;
use App\Common\Enums\ProductTypeEnums;
use App\Common\Tools\CustomException;
use App\Models\ChannelModel;
use App\Sdks\Fq\FqSdk;
use App\Services\Cp\Book\FqBookService;
use App\Services\Cp\Chapter\FqChapterService;


class FqChannelServiceAbstract extends AbstractCpChannelService
{

    protected $cpType = CpTypeEnums::FQ;

    public function sync(){

        $productList = $this->getProductList();

        if($this->getParam('channel_ids')){
            $this->syncById();
            return;
        }

        foreach ($productList as $product){

            $startTime = $this->getParam('start_date').' 00:00:00';
            $endTime = $this->getParam('end_date').' 23:59:59';
            if($product['type'] == ProductTypeEnums::KYY){
                $sdk = $this->getSdk($product);
                $offset = 0;
                do{
                    $list  = $sdk->getChannelList($startTime,$endTime,$offset);
                    foreach ($list['result'] as $item){
                        $this->saveItem($product,$item);
                    }
                    $offset = $list['next_offset'];

                }while($list['has_more']);
            }
        }
    }

    /**
     * 根据ID 同步
     */
    public function syncById(){

        $channelIds = $this->getParam('channel_ids');
        $channelList = (new ChannelModel())->whereIn('id',$channelIds)->get();
        foreach ($channelList as $channel){
            $product = $channel->product;
            if($product['type'] == ProductTypeEnums::H5){
                throw new CustomException([
                    'code' => 'NO_SUPPORT',
                    'message' => '暂不支持更新',
                ]);
            }

            $sdk = $this->getSdk($product);
            $list  = $sdk->readChannel($channel['cp_channel_id']);

            foreach ($list['result'] as $item){
                $this->saveItem($product,$item);
            }
        }

    }

    /**
     * @param $product
     * @return FqSdk
     * 获取sdk
     */
    public function getSdk($product){
        return new FqSdk($product['cp_product_alias'],$product['cp_secret']);
    }

    /**
     * @param $product
     * @param $item
     * @throws CustomException
     * 保存
     */
    public function saveItem($product,$item){
        //书籍信息
        $book = (new FqBookService())->setProduct($product)->read($item['book_id']);
        //章节信息
        $chapterService = (new FqChapterService())->setProduct($product)->setBook($book);
        $chapter = $chapterService->readSave($item['chapter_id'],$item['chapter_title'],$item['chapter_order']);
        //入库
        $this->save([
            'product_id'    => $product['id'],
            'cp_channel_id' => $item['promotion_id'],
            'name'          => $item['promotion_name'],
            'book_id'       => $book['id'],
            'chapter_id'    => $chapter['id'] ?? 0,
            'force_chapter_id' => 0,
            'extends'       => [
                'hap_url'   => $item['promotion_url'],
            ],
            'create_time' => $item['create_time'],
            'updated_time'=> $item['create_time'],
        ]);
    }
}
