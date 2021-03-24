<?php

namespace App\Services\Yw;


use App\Common\Services\ConsoleEchoService;
use App\Common\Services\ErrorLogService;
use App\Common\Tools\CustomException;
use App\Datas\BookData;
use App\Models\BookModel;
use App\Sdks\Yw\YwSdk;

class BookService extends YwService
{

    /**
     * constructor.
     */
    public function __construct(){
        parent::__construct();

        $this->setModel(new BookModel());
    }


    public function syncBook(){

        $productList = $this->getProductList();

        $consoleEchoService = new ConsoleEchoService();

        $bookData = new BookData();

        foreach ($productList as $product){
            $consoleEchoService->echo($product['name']);
            $sdk = new YwSdk($product['cp_product_alias'],$product['cp_account']['account'],$product['cp_account']['cp_secret']);
            $cpBookIds = $sdk->getBookIds();

            $count = count($cpBookIds);
            foreach ($cpBookIds as $i => $cpBookId){
                $consoleEchoService->incrOffset();
                try{
                    $consoleEchoService->progress($count,$i,$cpBookId);

                    $info = $sdk->getBookInfo($cpBookId);
                    $bookData->save([
                        'cp_type'       => $product['cp_type'],
                        'cp_book_id'    => $info['cbid'],
                        'name'          => $info['title'],
                        'author_name'   => $info['author_name'],
                        'all_words'     => $info['all_words'],
                        'update_time'   => $info['update_time']
                    ]);
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
