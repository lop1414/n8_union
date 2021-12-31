<?php

namespace App\Services\Cp\Chapter;


use App\Common\Enums\CpTypeEnums;
use App\Common\Tools\CustomException;
use App\Sdks\Yw\YwSdk;

class YwChapterService extends AbstractCpChapterService
{
    protected $cpType = CpTypeEnums::YW;

    /**
     * @return array
     * @throws CustomException
     */
    public function sync(){
        $cpBookId = $this->getParam('cp_book_id');
        $cpChapterId = $this->getParam('cp_chapter_id');
        $this->checkProduct();
        $sdk = new YwSdk($this->product['cp_product_alias'],$this->product['cp_account']['account'],$this->product['cp_account']['cp_secret']);

        $list = $sdk->getChapterList($cpBookId);
        $list = $list['chapter_list'] ?? [];
        $info = [];

        foreach ($list as $chapter){
            $item = $this->chapterModelData->save([
                'book_id'       => $this->book['id'],
                'cp_chapter_id' => $chapter['ccid'],
                'name'          => $chapter['chapter_title'],
                'seq'           => $chapter['chapter_seq']
            ]);
            if($cpChapterId && $cpChapterId == $chapter['ccid']){
                $info = $item;
            }
        }
        return $info;
    }


}
