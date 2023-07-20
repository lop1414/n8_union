<?php

namespace App\Services\Cp\Chapter;

use App\Common\Enums\CpTypeEnums;
use App\Common\Sdks\YwFx\YwFxSdk;
use App\Models\ProductModel;
use App\Services\BookService;
use App\Services\ChapterService;

class YwdjChapterService implements CpChapterInterface
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
        return CpTypeEnums::YWDJ;
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
        $sdk = new YwFxSdk($product['cp_product_alias'],$product['cp_account']['account'],$product['cp_account']['cp_secret']);

        $videoInfo = $this->bookService->read($bookId);

        $res = $sdk->getChapterList($videoInfo['cp_book_id']);
        $list = $res['chapter_list'] ?? [];

        $data = [];
        foreach ($list as $chapter){
            $data[] = [
                'book_id'       => $bookId,
                'cp_chapter_id' => $chapter['chapter_id'],
                'name'          => $chapter['chapter_name'],
                'seq'           => $chapter['chapter_seq']
            ];

        }
        return $data;
    }
}
