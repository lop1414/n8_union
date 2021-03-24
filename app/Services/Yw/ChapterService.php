<?php

namespace App\Services\Yw;

use App\Common\Services\ConsoleEchoService;
use App\Common\Services\ErrorLogService;
use App\Common\Tools\CustomException;
use App\Datas\ChapterData;
use App\Models\ChapterModel;
use App\Models\BookModel;
use App\Sdks\Yw\YwSdk;

class ChapterService extends YwService
{

    /**
     * constructor.
     */
    public function __construct(){
        parent::__construct();

        $this->setModel(new ChapterModel());
    }


    public function sync(){

        $productList = $this->getProductList();

        $consoleEchoService = new ConsoleEchoService();

        $chapterData = new ChapterData();
        foreach ($productList as $product){
            $consoleEchoService->echo($product['name']);

            $bookList = (new BookModel())
                ->where('cp_type',$this->cpType)
                ->get();
            $count = count($bookList);

            $sdk = new YwSdk($product['cp_product_alias'],$product['cp_account']['account'],$product['cp_account']['cp_secret']);
            foreach ($bookList as $i => $book){

                $consoleEchoService->incrOffset();
                try{
                    $consoleEchoService->progress($count,$i,$book['id']);

                    $list = $sdk->getChapterList($book['cp_book_id']);
                    $list = $list['chapter_list'] ?? [];

                    foreach ($list as $chapter){
                        $chapterData->save([
                            'book_id'       => $book['id'],
                            'cp_chapter_id' => $chapter['ccid'],
                            'name'          => $chapter['chapter_title'],
                            'seq'           => $chapter['chapter_seq']
                        ]);
                    }

                }catch (CustomException $e){

                    // 日志
                    (new ErrorLogService())->catch($e);

                    // 提示
                    $msg = json_decode($e->getMessage(),true);
                    $consoleEchoService->incrOffset();
                    $consoleEchoService->error($msg['data']['result']['msg'] ?? $msg['message']);
                    $consoleEchoService->decrOffset();
                }
                $consoleEchoService->decrOffset();
            }
        }
        $consoleEchoService->echo('');
    }
}
