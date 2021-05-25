<?php

namespace App\Services;

use App\Common\Services\BaseService;
use App\Common\Tools\CustomException;
use App\Common\Tools\CustomRedis;
use App\Models\LotteryModel;

class LotteryService extends BaseService
{
    /**
     * @var string
     * 缓存前缀
     */
    protected $cachePrefix = 'lottery';

    /**
     * @var CustomRedis
     * 缓存驱动
     */
    protected $cacheDriver;

    public function __construct(){
        parent::__construct();

        $this->cacheDriver = new CustomRedis();
    }

    /**
     * @param $lotteryId
     * @return mixed
     * @throws CustomException
     * 查找
     */
    public function found($lotteryId){
        $lottery = LotteryModel::find($lotteryId);
        if(empty($lottery)){
            throw new CustomException([
                'code' => 'NOT_FOUND_LOTTERY',
                'message' => '找不到该抽奖',
            ]);
        }

        // 关联抽奖奖品
        $lottery->lottery_prizes;

        return $lottery;
    }

    /**
     * @param $lotteryId
     * @return string
     * 获取 key
     */
    public function getKey($lotteryId){
        return $this->buildKey($this->cachePrefix, [$lotteryId]);
    }

    /**
     * @param $lotteryId
     * @return |null
     * @throws CustomException
     * 获取缓存
     */
    public function getCache($lotteryId){
        $key = $this->getKey($lotteryId);

        $data = $this->cacheDriver->get($key);
        if(empty($data)){
            $data = $this->setCache($lotteryId);
        }

        return $data;
    }

    /**
     * @param $lotteryId
     * @param null $cache
     * @return |null
     * @throws CustomException
     * 设置缓存
     */
    public function setCache($lotteryId, $cache = null){
        $key = $this->getKey($lotteryId);

        if(empty($cache)){
            $lottery = $this->found($lotteryId);
            if(!empty($lottery->release_data)){
                $cache = $lottery->release_data;
            }
        }

        if(!empty($cache)){
            $this->cacheDriver->set($key, $cache);
        }

        return $cache;
    }

    /**
     * @param $lotteryId
     * @return bool
     * @throws CustomException
     * 发布
     */
    public function release($lotteryId){
        $lottery = $this->found($lotteryId);

        // 验证概率是否100%
        $chanceTotal = 0;
        foreach($lottery->lottery_prizes as $prize){
            $chanceTotal += $prize->chance;
        }
        if($chanceTotal != 100){
            throw new CustomException([
                'code' => 'PRIZE_CHANCE_TOTAL_MUST_BE_100',
                'message' => '奖品几率总和必须为100',
                'data' => [
                    'lottery' => $lottery,
                ],
            ]);
        }

        // 去除发布字段
        $lottery = $lottery->expandExtendsField($lottery);
        unset($lottery->release_data, $lottery->release_at);

        // 关联抽奖奖品
        $lottery->lottery_prizes;
        foreach($lottery->lottery_prizes as $k => $prize){
            $lottery->lottery_prizes[$k] = $prize->expandExtendsField($prize);
        }

        // 更新发布数据
        $lottery->release_data = $lottery->toArray();
        $lottery->release_at = date('Y-m-d H:i:s', TIMESTAMP);
        $lottery->save();

        // 更新缓存
        $this->setCache($lotteryId, $lottery->release_data);

        return true;
    }

    public function draw($lotteryId){
        $lottery = $this->getCache($lotteryId);

        $datetime = date('Y-m-d H:i:s', TIMESTAMP);

        if($datetime > $lottery['end_at']){
            throw new CustomException([
                'code' => 'LOTTERY_WAS_END',
                'message' => '活动已结束',
            ]);
        }

        if($datetime < $lottery['start_at']){
            throw new CustomException([
                'code' => 'LOTTERY_NOT_YET_START',
                'message' => '活动尚未开始',
            ]);
        }
    }
}
