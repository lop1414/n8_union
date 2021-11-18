<?php

namespace App\Services\Cp\Channel;


use App\Common\Enums\CpTypeEnums;
use App\Common\Enums\ProductTypeEnums;
use App\Common\Tools\CustomException;
use App\Sdks\Qy\QySdk;
use App\Services\Cp\Book\QyBookService;
use App\Services\Cp\Chapter\QyChapterService;

class QyChannelService extends CpChannelBaseService
{

    protected $cpType = CpTypeEnums::QY;


    public function sync(){

        if($this->getParam('channel_ids')){
            $this->syncById();
            return;
        }

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
     * @throws CustomException
     * 保存
     */
    public function saveItem($product,$data){
        $bookId = substr($data['entry_page_chapter_id'],0,strlen($data['entry_page_chapter_id'])-5);
        $book = (new QyBookService())->setProduct($product)->read($bookId);

        $chapterService = (new QyChapterService())->setProduct($product)->setBook($book);
        $chapter = $chapterService->readSave($data['entry_page_chapter_id'],'',$data['entry_page_chapter_idx']);
        $forceChapter = $chapterService->readSave('','',$data['subscribe_chapter_idx']);

        $this->save([
            'product_id'     => $product['id'],
            'cp_channel_id'  => $data['id'],
            'name'           => $data['dispatch_channel'],
            'book_id'        => $book['id'],
            'chapter_id'     => $chapter['id'] ?? 0,
            'force_chapter_id'  => $forceChapter['id'] ?? 0,
            'extends'       => [],
            'create_time'    => date('Y-m-d H:i:s',$data['createtime']),
            'updated_time'   => date('Y-m-d H:i:s',$data['createtime']),
        ]);
    }





    public function syncById(){
        throw new CustomException([
            'code' => 'NO_SUPPORT',
            'message' => '暂不支持更新',
        ]);
    }
}
