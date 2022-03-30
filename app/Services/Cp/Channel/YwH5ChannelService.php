<?php

namespace App\Services\Cp\Channel;


use App\Common\Enums\CpTypeEnums;
use App\Common\Enums\ProductTypeEnums;
use App\Common\Tools\CustomException;
use App\Models\ProductModel;
use App\Sdks\Yw\YwSdk;
use App\Services\BookService;
use App\Services\ChapterService;
use App\Services\Cp\Book\YwBookService;
use App\Services\Cp\Chapter\YwChapterService;


class YwH5ChannelService implements CpChannelInterface
{

    protected $bookService;
    protected $chapterService;


    public function __construct()
    {
        $this->bookService = new BookService();
        $this->chapterService = new ChapterService();
    }

    public function getType(): string
    {
        return ProductTypeEnums::H5;
    }

    public function getCpType(): string
    {
        return CpTypeEnums::YW;
    }

    public function get($product, $date, $cpId): array
    {
        $sdk = new YwSdk($product['cp_product_alias'],$product['cp_account']['account'],$product['cp_account']['cp_secret']);
        $startTime = $date.' 00:00:00';
        $endTime = $date.' 23:59:59';
        $data = array();

        $currentTotal = 0;
        $page = 1;
        do{
            $list  = $sdk->getH5ChannelList($startTime,$endTime,$page,$cpId);

            $total = $list['total_count'];
            $currentTotal += count($list['list']);
            foreach ($list['list'] as $item){
                //书籍信息
                $book = $this->readBook($product,$item['cbid']);
                //章节信息
                $chapter = $this->readChapter($product,$book['id'],$item['ccid']);
                $forceChapter = $this->chapterService->readBySeq($book['id'],$item['force_chapter']);

                $data[] = [
                    'product_id'    => $product['id'],
                    'cp_channel_id' => $item['channel_id'],
                    'name'          => $item['channel_name'],
                    'book_id'       => $book['id'],
                    'chapter_id'    => $chapter['id'] ?? 0,
                    'force_chapter_id' => $forceChapter['id'] ?? 0,
                    'extends'       => [
                        'force_subscribe' => $item['force_subscribe'],
                        'url'             => $item['url'],
                        'short_url'       => $item['short_url']
                    ],
                    'create_time' => date('Y-m-d H:i:s',$item['create_time']),
                    'updated_time' => date('Y-m-d H:i:s',$item['create_time']),
                ];
            }
            $page += 1;

        }while($currentTotal < $total);

        return $data;
    }

    protected function readBook(ProductModel $product, string $cpBookId): array
    {
        $cpType = $this->getCpType();
        $info = $this->bookService->readByUniqueKey($cpBookId,$cpType);
        if(empty($info)){
            $readData = (new YwBookService())->read($product,$cpBookId);
            $info = $this->bookService->save($readData);
        }
        return $info;
    }

    protected function readChapter(ProductModel $product, int $bookId, string $cpChapterId): array
    {
        $info = $this->chapterService->readByUniqueKey($bookId,$cpChapterId);
        if(empty($info)){
            (new YwChapterService())->sync($product,$bookId);
            $info = $this->chapterService->readByUniqueKey($bookId,$cpChapterId);
        }
        return $info;
    }


    public function create($product, $name, $book, $chapter,$forceChapter): string
    {
        throw new CustomException([
            'code'       => 'NOT_CAN_CREATE_CHANNEL',
            'message'    => "该小说平台暂不支持"
        ]);
    }
}
