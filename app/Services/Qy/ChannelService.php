<?php

namespace App\Services\Qy;


use App\Common\Enums\ProductTypeEnums;
use App\Common\Tools\CustomException;
use App\Models\ChannelModel;
use App\Sdks\Qy\QySdk;



class ChannelService extends QyService
{

    /**
     * constructor.
     */
    public function __construct(){
        parent::__construct();

        $this->setModel(new ChannelModel());
    }


    public function sync($startDate,$endDate,$productId = null,$channelIds = []){
        $where = $productId ? ['id'=>$productId] : [];
        $productList = $this->getProductList($where);

        foreach ($productList as $product){

            $date = $startDate;
            do{

                if($product['type'] == ProductTypeEnums::H5){
                    if(empty($channelIds)){
                        $this->syncItem($date,$product);
                    }else{
                        $this->syncItemById($product,$channelIds);
                    }
                }

                $date = date('Y-m-d',  strtotime('+1 day',strtotime($date)) );
            }while($date <= $endDate);

        }
    }


    public function syncItem($date,$product){
        $sdk = new QySdk($product['cp_secret']);
        $page = 1;

        do{
            $list  = $sdk->getChannelList($date,$page);
            $totalPage = $list['last_page'];
            foreach ($list['data'] as $item){
                $channel = (new ChannelModel())
                    ->where('product_id',$product['id'])
                    ->where('cp_channel_id',$item['channel_id'])
                    ->first();

                if(empty($channel)){
                    $channel = new ChannelModel();
                }

                $bookId = substr($item['entry_page_chapter_id'],0,strlen($item['entry_page_chapter_id'])-5);
                $book = (new BookService())->setProduct($product)->read($bookId,$item['book_name']);

                $chapterService = (new ChapterService())->setProduct($product)->setBook($book);
                $chapter = $chapterService->read($item['entry_page_chapter_id'],'',$item['entry_page_chapter_idx']);
                $forceChapter = $chapterService->readBySeq($item['subscribe_chapter_idx'],'','');


                $channel->product_id = $product['id'];
                $channel->cp_channel_id = $item['channel_id'];
                $channel->name = $item['dispatch_channel'];
                $channel->book_id = $book['id'];
                $channel->chapter_id = $chapter['id'] ?? 0;
                $channel->force_chapter_id = $forceChapter['id'] ?? 0;
                $channel->extends = [];
                $channel->create_time = date('Y-m-d H:i:s',$item['createtime']);
                $channel->updated_time = date('Y-m-d H:i:s',$item['createtime']);
                $channel->save();

            }
            $page += 1;

        }while($page <= $totalPage);
    }


    public function syncItemById($product,$channelIds){
        throw new CustomException([
            'code' => 'NO_SUPPORT',
            'message' => '该平台不支持根据ID更新',
        ]);
    }
}
