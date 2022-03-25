<?php

namespace App\Services\Cp\Book;


use App\Models\ProductModel;
use App\Sdks\Yw\YwSdk;


class YwBookService
{



    public function read(ProductModel $product,string $cpId): array
    {
        $sdk = new YwSdk($product['cp_product_alias'],$product['cp_account']['account'],$product['cp_account']['cp_secret']);

        $data = $sdk->getBookInfo($cpId);
        $info = [
            'cp_type'       => $product['cp_type'],
            'cp_book_id'    => $data['cbid'],
            'name'          => $data['title'],
            'author_name'   => $data['author_name'],
            'all_words'     => $data['all_words'],
            'update_time'   => $data['update_time']
        ];

        return $info;
    }
}
