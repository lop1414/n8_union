<?php

namespace App\Services\Cp\Channel;


use App\Common\Enums\CpTypeEnums;
use App\Common\Enums\ProductTypeEnums;
use App\Common\Sdks\Zy\ZySdk;
use App\Services\BookService;
use App\Services\ChapterService;
use App\Services\ReadSignService;


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
        $sdk = new ZySdk($product['cp_product_alias'],$product['cp_secret'],$product['extends']['api_alias']);
        $bookService = new BookService();
        $chapterService = new ChapterService();

        $para = [
            'time'  => TIMESTAMP
        ];
        $channels = $sdk->getChannels($para);

        $readSignService = new ReadSignService();
        foreach ($channels['data'] as $channel){
            // 书籍
            $book['id'] = 0;
            $readSign = [];
            if(!empty($channel['bid'])){
                $book = $bookService->readSave([
                    'cp_book_id' => $channel['bid'],
                    'name'       => $channel['book_name'],
                    'cp_type'    => $product['cp_type']
                ]);

                $readSignData = ['book_id' => $book['id']];
                $readSignName = $book['name'];
                for($i = 1;$i < 4;$i++){
                    $tmpChapterId = $book['id'].'_'.$channel['sign_num_'.$i];
                    $chapter = $chapterService->readSave($book['id'],$tmpChapterId,$channel['sign_num_'.$i.'_name'],$channel['sign_num_'.$i]);
                    $readSignData['sign_chapter_id_'.$i] = $chapter['id'];
                    $readSignName .= '_'.($channel['sign_num_'.$i]+1);
                }
                $readSignData['name'] = $readSignName;
                $readSign = $readSignService->saveWithGet($readSignData);
            }

            // 打开章节
//            $openChapter = $chapterService->readSave($book['id'],0,$channel['num_name'],$channel['num']);

            //强制章节
//            $installChapter = $chapterService->readSave($book['id'],0,$channel['follow_num_name'],$channel['follow_num']);

            $data[] = [
                'product_id'     => $product['id'],
                'cp_channel_id'  => $channel['id'],
                'name'           => $channel['name'],
                'book_id'        => $book['id'] ?? 0,
                'chapter_id'     => 0,
                'force_chapter_id'  => 0,
                'read_sign_id'  => $readSign['id'] ?? 0,
                'extends'       => [
                    'hap_url'   => $channel['hap_links'],
                    'h5_url'    => $channel['links'],
                    'http_url'    => $channel['hapjs_links'],
                ],
                'create_time'    => $channel['created_at'],
                'updated_time'   => $channel['created_at'],
            ];
        }

        return $data;
    }
}
