<?php

namespace App\Services\Cp\Channel;


use App\Common\Enums\CpTypeEnums;
use App\Common\Enums\ProductTypeEnums;
use App\Common\Sdks\YwFx\YwFxSdk;
use App\Models\ProductModel;
use App\Services\BookService;
use App\Services\ChapterService;
use App\Services\Cp\Book\YwdjVideoService;
use App\Services\Cp\Chapter\YwdjChapterService;


class YwdjWeChatMiniProgramChannelService implements CpChannelInterface
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
        return ProductTypeEnums::WECHAT_MINI_PROGRAM;
    }

    public function get($product,$date,$cpId): array
    {
        //不支持cp id 获取

        $sdk = $this->getSdk($product);
        $startTime = $date.' 00:00:00';
        $endTime = $date.' 23:59:59';
        $data = array();
        $currentTotal = 0;
        $page = 1;
        do{
            $list  = $sdk->getChannelList($product['cp_product_alias'],$startTime,$endTime,$page);

            foreach ($list['list'] as $item){
                $currentTotal += 1;

                //书籍信息
                $book = $this->readVideo($product,$item['video_id']);
                //章节信息
                $chapter = $this->readChapter($product,$book['id'],$item['chapter_id']);
                $forceChapter = $this->chapterService->readBySeq($book['id'],$item['force_chapter']);

                $data[] = [
                    'product_id'    => $product['id'],
                    'cp_channel_id' => $item['channel_id'],
                    'name'          => $item['channel_name'],
                    'book_id'       => $book['id'],
                    'chapter_id'    => $chapter['id'] ?? 0,
                    'force_chapter_id' => $forceChapter['id'] ?? 0,
                    'extends'       => [
                        'page_path' => $item['path'],
                        'h5_url'    => $item['url_link_h5'],
                        'http_url'  => $item['url_link']
                    ],
                    'create_time' => $item['create_time'],
                    'updated_time' => $item['create_time'],
                ];
            }
            $page += 1;

        }while($currentTotal < $list['total_count']);
        return $data;
    }

    public function getSdk(ProductModel $product): YwFxSdk
    {
        return new YwFxSdk($product['cp_product_alias'],$product['cp_account']['account'],$product['cp_account']['cp_secret']);
    }

    protected function readVideo(ProductModel $product,string $videoId): array
    {
        $cpType = $this->getCpType();
        $info = $this->bookService->readByUniqueKey($videoId,$cpType);
        if(!$info){
            $readData = (new YwdjVideoService())->read($product,$videoId);
            $info = $this->bookService->save($readData);
        }
        return $info;
    }

    public function getCpType(): string
    {
        return CpTypeEnums::YWDJ;
    }

    protected function readChapter(ProductModel $product,int $bookId,string $cpChapterId): ?array
    {
        $info = $this->chapterService->readByUniqueKey($bookId,$cpChapterId);

        if(!$info){
            (new YwdjChapterService())->sync($product,$bookId);
            $info = $this->chapterService->readByUniqueKey($bookId,$cpChapterId);
        }
        return $info;
    }
}
