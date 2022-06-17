<?php

namespace App\Services\Cp\Channel;


use App\Common\Enums\CpTypeEnums;
use App\Common\Enums\ProductTypeEnums;
use App\Common\Sdks\Zy\ZySdk;
use App\Common\Tools\CustomException;
use App\Services\BookService;
use App\Services\ChapterService;


class ZyKyyChannelService implements CpChannelInterface
{


    public function getCpType(): string
    {
        return CpTypeEnums::ZY;
    }



    public function getType(): string
    {
        return ProductTypeEnums::KYY;
    }



    public function get($product, $date, $cpId): array
    {
        //不支持cp id 获取

        $data = array();
        $sdk = new ZySdk($product['cp_product_alias'],$product['cp_secret']);
        $bookService = new BookService();
        $chapterService = new ChapterService();

        $para = [
            'time'  => TIMESTAMP
        ];
        $channels = $sdk->getChannels($para);

        foreach ($channels as $channel){
            // 书籍
            $book = $bookService->readSave([
                'cp_book_id' => $channel['bid'],
                'name'       => $channel['book_name'],
                'cp_type'    => $product['cp_type']
            ]);
            // 打开章节
//            $openChapter = $chapterService->readSave($book['id'],0,$channel['num_name'],$channel['num']);

            //强制章节
//            $installChapter = $chapterService->readSave($book['id'],0,$channel['follow_num_name'],$channel['follow_num']);


            $data[] = [
                'product_id'     => $product['id'],
                'cp_channel_id'  => $channel['id'],
                'name'           => $channel['name'],
                'book_id'        => $book['id'],
                'chapter_id'     => 0,
                'force_chapter_id'  => 0,
                'extends'       => [
//                    'hap_url'   => $channel['hap_links'],
//                    'h5_url'    => $channel['links']
                ],
                'create_time'    => $channel['created_at'],
                'updated_time'   => $channel['created_at'],
            ];
        }

        return $data;
    }

    public function create($product, $name, $book, $chapter,$forceChapter): string
    {
        throw new CustomException([
            'code'       => 'NOT_CAN_CREATE_CHANNEL',
            'message'    => "该小说平台暂不支持"
        ]);
    }
}
