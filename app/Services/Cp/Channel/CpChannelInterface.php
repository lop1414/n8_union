<?php

namespace App\Services\Cp\Channel;

use App\Models\BookModel;
use App\Models\ChapterModel;
use App\Models\ProductModel;

interface CpChannelInterface
{
    /**
     * @return string
     * 获取平台类型
     */
    public function getCpType(): string;

    /**
     * @return string
     * 获取产品类型
     */
    public function getType(): string;

    /**
     * @param ProductModel $product
     * @param string $date
     * @param string $cpId
     * @return array
     * return
     */
    public function get(ProductModel $product,string $date,string $cpId): array;


    /**
     * @param ProductModel $product
     * @param string $name
     * @param BookModel $book
     * @param ChapterModel $chapter
     * @param ChapterModel $forceChapter
     * @return string
     * 创建
     */
//    public function create(ProductModel $product,string $name,BookModel $book,ChapterModel $chapter,ChapterModel $forceChapter): string;

}
