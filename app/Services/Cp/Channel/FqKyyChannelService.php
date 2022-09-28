<?php

namespace App\Services\Cp\Channel;


use App\Common\Enums\CpTypeEnums;
use App\Common\Enums\ProductTypeEnums;
use App\Models\ProductModel;
use App\Common\Sdks\Fq\FqSdk;
use App\Services\BookService;
use App\Services\ChapterService;
use App\Services\Cp\Book\FqBookService;


class FqKyyChannelService implements CpChannelInterface
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
        return CpTypeEnums::FQ;
    }

    public function getType(): string
    {
        return ProductTypeEnums::KYY;
    }


    protected function getSdk(ProductModel $product){
        return new FqSdk($product['cp_account']['account'],$product['cp_account']['cp_secret']);
    }


    public function get($product, $date, $cpId): array
    {

        $data = array();
        $sdk = $this->getSdk($product);
        $offset = 0;
        do{
            if($cpId){
                $list = $sdk->readChannel($cpId);
            }else{
                $startTime = $date.' 00:00:00';
                $endTime = $date.' 23:59:59';
                $list = $sdk->getChannelList($startTime,$endTime,$offset);
            }

            foreach ($list['result'] as $item){
                //书籍信息
                $book = $this->readBook($product,$item['book_id']);
                //章节信息
                $chapter = $this->chapterService->readSave($book['id'],$item['chapter_id'],$item['chapter_title'],$item['chapter_order']);

                $data[] = [
                    'product_id'    => $product['id'],
                    'cp_channel_id' => $item['promotion_id'],
                    'name'          => $item['promotion_name'],
                    'book_id'       => $book['id'],
                    'chapter_id'    => $chapter['id'] ?? 0,
                    'force_chapter_id' => 0,
                    'extends'       => [
                        'hap_url'   => $item['promotion_url'],
                        'h5_url'   => 'https://novel.snssdk.com/page/novelsale/openquickapp?schema='.urlencode($item['promotion_url']),
                    ],
                    'create_time' => $item['create_time'],
                    'updated_time'=> $item['create_time'],
                ];
            }
            $offset = $list['next_offset'];

        }while($list['has_more']);

        return $data;
    }

    protected function readBook(ProductModel $product,string $cpId): array
    {
        $cpType = $this->getCpType();
        $info = $this->bookService->readByUniqueKey($cpId,$cpType);
        if(empty($info)){
            $readData = (new FqBookService())->read($product,$cpId);
            $info = $this->bookService->save($readData);
        }
        return $info;
    }



    public function create($product, $name, $book, $chapter,$forceChapter,$cpAdminAccount = null): string
    {
        $sdk = $this->getSdk($product);
        $optimizerAccount = $cpAdminAccount ? $cpAdminAccount->extends->optimizer_account : null;
        $res = $sdk->createChannel($name,$book['cp_book_id'],$chapter['seq'],$optimizerAccount);
        return $res['promotion_id'];
    }
}
