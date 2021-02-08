<?php



class CacheTest extends \TestCase
{


    /**
     * 清除缓存
     */
    public function testClearCache(){
        if(env('APP_DEBUG') && env('APP_ENV') == 'testing') {
            $redis = new \App\Common\Tools\CustomRedis();
            $redis->flushdb();
        }
        $this->assertTrue(true);
    }
}
