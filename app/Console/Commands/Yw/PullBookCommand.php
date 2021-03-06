<?php

namespace App\Console\Commands\Yw;

use App\Common\Console\BaseCommand;
use App\Services\Yw\BookService;

class PullBookCommand extends BaseCommand
{
    /**
     * 命令行执行命令
     * @var string
     */
    protected $signature = 'yw:pull_book';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '阅文书籍';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * 处理
     */
    public function handle(){
        $servise = new BookService();
        $servise->syncBook();
    }
}
