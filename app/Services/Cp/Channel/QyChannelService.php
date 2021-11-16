<?php

namespace App\Services\Cp\Channel;


use App\Common\Enums\CpTypeEnums;
use App\Common\Enums\ProductTypeEnums;
use App\Common\Tools\CustomException;
use App\Models\ChannelModel;
use App\Sdks\Qy\QySdk;
use App\Services\Qy\BookService;
use App\Services\Qy\ChapterService;

class QyChannelService extends CpChannelBaseService
{

    protected $cpType = CpTypeEnums::QY;


    public function sync(){
        $startDate = $this->getParam('start_date');
        $endDate = $this->getParam('end_date');

        $productList = $this->getProductList();

        foreach ($productList as $product){

            $date = $startDate;
            $sdk = new QySdk($product['cp_secret']);
            do{

                if($product['type'] == ProductTypeEnums::H5){
                    $page = 1;
                    do{
                        $list  = $sdk->getChannelList($date,$page);
                        $totalPage = $list['last_page'];
                        foreach ($list['data'] as $item){
                            $this->saveItem($product,$item);
                        }
                        $page += 1;

                    }while($page <= $totalPage);
                }

                $date = date('Y-m-d',  strtotime('+1 day',strtotime($date)));
            }while($date <= $endDate);

        }
    }



    /**
     * @param $product
     * @param $data
     * 保存
     */
    public function saveItem($product,$data){
        $channel = (new ChannelModel())
            ->where('product_id',$product['id'])
            ->where('cp_channel_id',$data['channel_id'])
            ->first();

        if(empty($channel)){
            $channel = new ChannelModel();
        }

        $bookId = substr($data['entry_page_chapter_id'],0,strlen($data['entry_page_chapter_id'])-5);
        $book = (new BookService())->setProduct($product)->read($bookId,$data['book_name']);

        $chapterService = (new ChapterService())->setProduct($product)->setBook($book);
        $chapter = $chapterService->read($data['entry_page_chapter_id'],'',$data['entry_page_chapter_idx']);
        $forceChapter = $chapterService->readBySeq($data['subscribe_chapter_idx'],'','');


        $channel->product_id = $product['id'];
        $channel->cp_channel_id = $data['id'];
        $channel->name = $data['dispatch_channel'];
        $channel->book_id = $book['id'];
        $channel->chapter_id = $chapter['id'] ?? 0;
        $channel->force_chapter_id = $forceChapter['id'] ?? 0;
        $channel->extends = [];
        $channel->create_time = date('Y-m-d H:i:s',$data['createtime']);
        $channel->updated_time = date('Y-m-d H:i:s',$data['createtime']);
        $channel->save();
    }





    public function syncById(){
        throw new CustomException([
            'code' => 'NO_SUPPORT',
            'message' => '该平台不支持根据ID更新',
        ]);
    }
}
