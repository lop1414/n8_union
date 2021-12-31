<?php

namespace App\Services\Cp\Channel;

use App\Common\Enums\CpTypeEnums;
use App\Common\Tools\CustomException;
use App\Datas\BookData;
use App\Datas\ChapterData;
use App\Sdks\Bm\BmSdk;

class BmChannelServiceAbstract extends AbstractCpChannelService
{

    protected $cpType = CpTypeEnums::BM;

    public function sync(){
        if($this->getParam('channel_ids')){
            $this->syncById();
            return;
        }

        $startDate = $this->getParam('start_date');
        $endDate = $this->getParam('end_date');

        $productList = $this->getProductList();

        $bookData = new BookData();
        $chapterData = new ChapterData();
        foreach ($productList as $product){
            $sdk = new BmSdk($product['cp_product_alias'],$product['cp_secret']);

            $parameter = [
                'page'  => 1
            ];
            if(!empty($startDate) && !empty($endDate)){
                $parameter['createTimeStart'] = strtotime($startDate.' 00:00:00');
                $parameter['createTimeEnd'] = strtotime($endDate.' 23:59:59');
            }

            do{
                $channels = $sdk->getCpChannel($parameter);

                foreach ($channels['list'] as $channel){

                    // 书籍
                    $book = $bookData->save([
                        'cp_type'       => $product['cp_type'],
                        'cp_book_id'    => $channel['novelid'],
                        'name'          => $channel['novelName'],
                        'author_name'   => '',
                        'all_words'     => 0,
                        'update_time'   => null
                    ]);
                    // 打开章节
                    $openChapter = $chapterData->save([
                        'book_id'       => $book['id'],
                        'cp_chapter_id' => $channel['openChapterid'],
                        'name'          => $channel['openChapterName'],
                        'seq'           => $channel['openChapterNumber']
                    ]);
                    //强制章节
                    $installChapter = $chapterData->save([
                        'book_id'       => $book['id'],
                        'cp_chapter_id' => $channel['installChapterid'],
                        'name'          => $channel['installChapterName'],
                        'seq'           => $channel['installChapterNumber']
                    ]);
                    //渠道
                    $this->save([
                        'product_id'     => $product['id'],
                        'cp_channel_id'  => $channel['channelid'],
                        'name'           => $channel['channelName'],
                        'book_id'        => $book['id'],
                        'chapter_id'     => $openChapter['id'],
                        'force_chapter_id'   => $installChapter['id'],
                        'extends'        => [],
                        'create_time'    => $channel['createTime'],
                        'updated_time'   => $channel['updateTime'],
                    ]);
                }

                $parameter['page'] += 1;
            }while($channels['totalPage'] >= $parameter['page']);
        }
    }


    public function syncById(){
        throw new CustomException([
            'code' => 'NO_SUPPORT',
            'message' => '暂不支持更新',
        ]);
    }
}
