<?php

namespace App\Services\Cp\Chapter;

use App\Common\Enums\CpTypeEnums;
use App\Models\ProductModel;
use App\Common\Sdks\Yw\YwSdk;
use App\Services\BookService;
use App\Services\ChapterService;

class YwChapterService implements CpChapterInterface
{
    protected $bookService;

    protected $chapterService;


    public function __construct()
    {
        $this->bookService = new BookService();
        $this->chapterService = new ChapterService();
    }



    public function getCpType(): string
    {
        return CpTypeEnums::YW;
    }

    /**
     * 同步
     * @param ProductModel $product
     * @param int $bookId
     */
    public function sync(ProductModel $product,int $bookId){
        $data = $this->get($product,$bookId);
        foreach ($data as $item){
            $this->chapterService->save($item);
        }
    }



    public function get(ProductModel $product,int $bookId):array
    {
        $sdk = new YwSdk($product['cp_product_alias'],$product['cp_account']['account'],$product['cp_account']['cp_secret']);

        $bookInfo = $this->bookService->read($bookId);

        $res = $sdk->getChapterList($bookInfo['cp_book_id']);
        $list = $res['chapter_list'] ?? [];

        $data = [];
        foreach ($list as $chapter){
            $data[] = [
                'book_id'       => $bookId,
                'cp_chapter_id' => $chapter['ccid'],
                'name'          => $chapter['chapter_title'],
                'seq'           => $chapter['chapter_seq']
            ];

        }
        return $data;
    }
}
