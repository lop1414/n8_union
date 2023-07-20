<?php

namespace App\Services\Cp\Channel;


use App\Common\Enums\CpTypeEnums;
use App\Common\Enums\ProductTypeEnums;
use App\Common\Sdks\Yg\YgSdk;
use App\Common\Tools\CustomException;
use App\Services\BookService;
use App\Services\ChapterService;


class YgWeChatMiniProgramChannelService implements CpChannelInterface
{


    public function getCpType(): string
    {
        return CpTypeEnums::YG;
    }



    public function getType(): string
    {
        return ProductTypeEnums::WECHAT_MINI_PROGRAM;
    }



    public function get($product, $date, $cpId): array
    {
        //不支持cp id 获取
        $data = array();
        $sdk = new YgSdk($product['cp_account']['account'],$product['cp_account']['cp_secret']);


        $bookService = new BookService();
        $chapterService = new ChapterService();

        $startTime = $date.' 00:00:00';
        $endTime = $date.' 23:59:59';


        $channels = $sdk->getChannelListByWeb($product['cp_secret'],$startTime,$endTime);

        if(!isset($channels['total'])){
            throw new CustomException([
                'code' => 'YG_WEB_REQUEST_ERROR',
                'message' => '请联系管理员'
            ]);
        }

        if($channels['total'] <= 0){
            return [];
        }

        foreach ($channels['rows'] as $item){
            // 书籍
            $book['id'] = 0;

            $book = $bookService->readSave([
                'cp_book_id' => $item['book_id'],
                'name'       => $item['book_name'],
                'cp_type'    => $product['cp_type']
            ]);

            // 打开章节
            $chapter = $chapterService->readSave($book['id'],$item['chapter_id'],$item['chapter_name'],$item['chapter_idx']??0);


            $data[] = [
                'product_id'    => $product['id'],
                'cp_channel_id' => $item['id'],
                'name'          => $item['name'],
                'book_id'       => $book['id'],
                'chapter_id'    => $chapter['id'] ?? 0,
                'force_chapter_id' =>  0,
                'extends'       => [
                    'h5_url'    => $item['short_url'],
                    'page_path' => $item['spreadLink']
                ],
                'create_time' => date('Y-m-d H:i:s',$item['createtime']),
                'updated_time' => date('Y-m-d H:i:s',$item['updatetime']),
            ];
        }

        return $data;
    }
}
