<?php

namespace App\Console\Commands;

use App\Common\Console\BaseCommand;
use App\Datas\BookData;
use App\Datas\ChapterData;
use App\Models\N8UnionUserModel;
use App\Models\ReadSignModel;
use Illuminate\Support\Facades\DB;


class TestCommand extends BaseCommand
{
    /**
     * 命令行执行命令
     * @var string
     */
    protected $signature = 'test';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '';






    public function handle(){

        $list = (new ReadSignModel())->get();
        $bookModelData = new BookData();
        $chapterModelData = new ChapterData();

        foreach ($list as $item){
            if(!empty($item['name'])) continue;
            $book =  $bookModelData->setParams(['id' => $item['book_id']])->read();
            $chapter1 = $chapterModelData->setParams(['id' => $item['sign_chapter_id_1']])->read();
            $chapter2 = $chapterModelData->setParams(['id' => $item['sign_chapter_id_2']])->read();
            $chapter3 = $chapterModelData->setParams(['id' => $item['sign_chapter_id_3']])->read();
            $item->name = $book['name'].'_'.($chapter1['seq']+1).'_'.($chapter2['seq']+1).'_'.($chapter3['seq']+1);
            $item->save();
        }
    }



}
