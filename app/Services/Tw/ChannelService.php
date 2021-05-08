<?php

namespace App\Services\Tw;


use App\Datas\BookData;
use App\Datas\ChannelData;
use App\Datas\ChapterData;
use App\Models\ChannelModel;
use App\Sdks\Tw\TwSdk;


class ChannelService extends TwService
{

    /**
     * constructor.
     */
    public function __construct(){
        parent::__construct();

        $this->setModel(new ChannelModel());
    }


    public function sync($startDate,$endDate,$productId = null){
        $where = $productId ? ['id'=>$productId] : [];
        $productList = $this->getProductList($where);

        $bookData = new BookData();
        $chapterData = new ChapterData();
        $channelData = new ChannelData();
        foreach ($productList as $product){
            echo "  {$product['name']}\n";
            $sdk = new TwSdk($product['cp_product_alias'],$product['cp_secret']);

            $parameter = [
                'time'  => TIMESTAMP,
            ];

            $date = $startDate = date('Ymd',strtotime($startDate));
            $endDate = date('Ymd',strtotime($endDate));

            do{
                echo "    {$date}\n";
                $parameter['adate'] = $date;

                $channels = $sdk->getCpChannel($parameter);

                foreach ($channels as $i => $channel){

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
                    //渠道
                    $channelData->save([
                        'product_id'     => $product['id'],
                        'cp_channel_id'  => $channel['id'],
                        'name'           => $channel['name'],
                        'book_id'        => $book['id'],
                        'chapter_id'     => $openChapter['id'],
                        'force_chapter_id'   => $installChapter['id'],
                        'create_time'    => $channel['created_at'],
                        'updated_time'   => $channel['created_at'],
                    ]);

                }
                $date = date('Ymd',strtotime('+1 day',strtotime($date)));
            }while($date <= $endDate);

        }
    }
}
