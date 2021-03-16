<?php

use \Illuminate\Support\Facades\DB;

class DropTableTest extends \TestCase
{
    public function testDropTable(){
        // 本地测试才截断表
        if(env('APP_DEBUG') && env('APP_ENV') == 'testing' && true){
            $this->dropN8Table();

            // 刷新缓存
            $this->artisan('refresh_cache --type=global_user');
            $this->artisan('refresh_cache --type=global_order');
        }
        $this->assertTrue(true);
    }

    public function dropN8Table(){
        DB::table('n8_global_users')->truncate();
        DB::table('n8_global_orders')->truncate();
        DB::table('n8_union_users')->truncate();
        DB::table('n8_union_user_extends')->truncate();
    }
}
