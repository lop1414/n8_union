<?php

namespace App\Services\Cp\Channel;


use App\Common\Enums\CpTypeEnums;
use App\Common\Enums\ProductTypeEnums;
use App\Models\ProductModel;
use App\Common\Sdks\Qy\QySdk;
use App\Services\BookService;
use App\Services\ChapterService;


class QyH5ChannelService implements CpChannelInterface
{

    protected $bookService;
    protected $chapterService;


    public function __construct()
    {
        $this->bookService = new BookService();
        $this->chapterService = new ChapterService();
    }

    public function getCpType(): string
    {
        return CpTypeEnums::QY;
    }

    public function getType(): string
    {
        return ProductTypeEnums::H5;
    }

    public function get($product, $date, $cpId): array
    {
        //不支持cp id 获取

        $data = array();

        $sdk = new QySdk($product['cp_secret']);
        $page = 1;
        do{
            $list  = $sdk->getChannelList($date,$page);
            $totalPage = $list['last_page'];
            foreach ($list['data'] as $item){
                $bookId = substr($item['entry_page_chapter_id'],0,strlen($item['entry_page_chapter_id'])-5);
                $book = $this->readBook($product,$bookId);

                $chapter = $this->chapterService->readSave($book['id'],$item['entry_page_chapter_id'],'',$item['entry_page_chapter_idx']);
                $forceChapter = $this->chapterService->readSave($book['id'],'','',$item['subscribe_chapter_idx']);

                $data[] = [
                    'product_id'     => $product['id'],
                    'cp_channel_id'  => $item['id'],
                    'name'           => $item['dispatch_channel'],
                    'book_id'        => $book['id'],
                    'chapter_id'     => $chapter['id'] ?? 0,
                    'force_chapter_id'  => $forceChapter['id'] ?? 0,
                    'extends'       => [],
                    'create_time'    => date('Y-m-d H:i:s',$item['createtime']),
                    'updated_time'   => date('Y-m-d H:i:s',$item['createtime']),
                ];
            }
            $page += 1;

        }while($page <= $totalPage);

        return $data;
    }

    public function readBook(ProductModel $product,$cpId): array
    {
        $cpType = $this->getCpType();
        $info = $this->bookService->readByUniqueKey($cpId,$cpType);
        return $info;
    }
}
