<?php

use \Illuminate\Support\Facades\DB;

class DropTableTest extends \TestCase
{
    public function testDropTable(){
        // 本地测试才截断表
        if(env('APP_DEBUG') && env('APP_ENV') == 'testing' && false){
            $this->dropN8Table();

        }
        $this->assertTrue(true);
    }

    public function dropN8Table(){
        DB::table('n8_global_users')->truncate();
        DB::table('n8_global_orders')->truncate();
    }
}
