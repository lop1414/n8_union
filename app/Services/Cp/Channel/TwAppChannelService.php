<?php

namespace App\Services\Cp\Channel;


use App\Common\Enums\CpTypeEnums;
use App\Common\Enums\ProductTypeEnums;
use App\Datas\BookData;
use App\Datas\ChapterData;
use App\Sdks\TwApp\TwAppSdk;


class TwAppChannelService implements CpChannelInterface
{


    public function getCpType(): string
    {
        return CpTypeEnums::TW;
    }



    public function getType(): string
    {
        return ProductTypeEnums::APP;
    }



    public function get($product, $date, $cpId): array
    {
        //不支持cp id 获取

        $data = array();
        $sdk = new TwAppSdk($product['cp_product_alias'],$product['cp_secret']);
        $bookData = new BookData();
        $chapterData = new ChapterData();

        $channels = $sdk->getCpChannel();

        foreach ($channels as $channel){
            // 书籍
            $book = $bookData->save([
                'cp_type'       => $product['cp_type'],
                'cp_book_id'    => $channel['book_id'] ?? 0,
                'name'          => $channel['name'],
                'author_name'   => '',
                'all_words'     => 0,
                'update_time'   => null
            ]);

            $data[] = [
                'product_id'     => $product['id'],
                'cp_channel_id'  => $channel['id'],
                'name'           => $channel['name'],
                'book_id'        => $book['id'],
                'chapter_id'     => 0,
                'force_chapter_id'  => 0,
                'extends'        => [],
                'create_time'    => null,
                'updated_time'   => null,
            ];
        }

        return $data;
    }
}
