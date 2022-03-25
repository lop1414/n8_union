<?php

namespace App\Services\Cp\Channel;


use App\Common\Enums\CpTypeEnums;
use App\Common\Enums\ProductTypeEnums;
use App\Datas\BookData;
use App\Datas\ChapterData;
use App\Sdks\Tw\TwSdk;


class TwKyyChannelService implements CpChannelInterface
{


    public function getCpType(): string
    {
        return CpTypeEnums::TW;
    }



    public function getType(): string
    {
        return ProductTypeEnums::KYY;
    }



    public function get($product, $date, $cpId): array
    {
        //不支持cp id 获取

        $data = array();
        $sdk = new TwSdk($product['cp_product_alias'],$product['cp_secret']);
        $bookData = new BookData();
        $chapterData = new ChapterData();

        $para = [
            'time'  => TIMESTAMP,
            'adate' => date('Ymd',strtotime($date))
        ];
        $channels = $sdk->getCpChannel($para);

        foreach ($channels as $channel){
            // 书籍
            $book = $bookData->save([
                'cp_type'       => $product['cp_type'],
                'cp_book_id'    => $channel['bid'],
                'name'          => $channel['book_name'],
                'author_name'   => '',
                'all_words'     => 0,
                'update_time'   => null
            ]);
            // 打开章节
            $openChapter = $chapterData->save([
                'book_id'       => $book['id'],
                'cp_chapter_id' => 0,
                'name'          => $channel['num_name'] ?? '',
                'seq'           => $channel['num']
            ]);
            //强制章节
            $installChapter = $chapterData->save([
                'book_id'       => $book['id'],
                'cp_chapter_id' => 0,
                'name'          => $channel['follow_num_name'] ?? '',
                'seq'           => $channel['follow_num']
            ]);


            $data[] = [
                'product_id'     => $product['id'],
                'cp_channel_id'  => $channel['id'],
                'name'           => $channel['name'],
                'book_id'        => $book['id'],
                'chapter_id'     => $openChapter['id'],
                'force_chapter_id'  => $installChapter['id'],
                'extends'       => [
                    'hap_url'   => $channel['hap_links'],
                    'h5_url'    => $channel['links']
                ],
                'create_time'    => $channel['created_at'],
                'updated_time'   => $channel['created_at'],
            ];
        }

        return $data;
    }
}
