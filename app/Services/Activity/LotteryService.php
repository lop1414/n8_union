<?php

namespace App\Services\Activity;

use App\Common\Enums\CycleTypeEnum;
use App\Common\Enums\ResponseCodeEnum;
use App\Common\Helpers\Functions;
use App\Common\Services\BaseService;
use App\Common\Tools\CustomException;
use App\Common\Tools\CustomRedis;
use App\Models\LotteryModel;
use App\Services\OpenUserService;

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

    /**
     * @var
     * 抽中奖品
     */
    protected $prize;

    /**
     *  constructor.
     * @throws CustomException
     */
    public function __construct(){
        parent::__construct();

        $this->cacheDriver = new CustomRedis();
    }

    /**
     * @return mixed
     * 获取奖品
     */
    public function getPrize(){
        return $this->prize;
    }

    /**
     * @param $prize
     * @return bool
     * 设置奖品
     */
    public function setPrize($prize){
        $this->prize = $prize;
        return true;
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

        // 更新发布数据
        $lottery->release_data = $this->getReleaseData($lotteryId);
        $lottery->release_at = date('Y-m-d H:i:s', TIMESTAMP);
        $lottery->save();

        // 更新缓存
        $this->setCache($lotteryId, $lottery->release_data);

        return true;
    }

    /**
     * @param $lotteryId
     * @return mixed
     * @throws CustomException
     * 获取发布数据
     */
    public function getReleaseData($lotteryId){
        $lottery = $this->found($lotteryId);

        // 去除发布字段
        $lottery = $lottery->expandExtendsField($lottery);
        unset($lottery->release_data, $lottery->release_at, $lottery->updated_at);

        // 关联抽奖奖品
        $lottery->lottery_prizes;
        foreach($lottery->lottery_prizes as $k => $prize){
            $lottery->lottery_prizes[$k] = $prize->expandExtendsField($prize);
        }

        return $lottery->toArray();
    }

    /**
     * @param $param
     * @return bool
     * @throws CustomException
     * 抽取
     */
    public function draw($param){
        $this->validRule($param, [
            'id' => 'required',
        ]);

        $openUserService = new OpenUserService();
        $openUser = $openUserService->info($param);
        if(empty($openUser)){
            throw new CustomException([
                'code' => 'NOT_FOUND_OPEN_USER',
                'message' => '用户ID尚未绑定',
            ]);
        }

        $lottery = $this->getCache($param['id']);

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

        $lockKey = "lottery|{$param['id']}|{$openUser['id']}";
        $runParam = [
            'n8_guid' => $openUser['n8_guid'],
            'lottery_id' => $param['id'],
            'lottery' => $lottery,
        ];

        $this->lockRun(
            [$this, 'run'],
            $lockKey,
            3600,
            ['log' => true],
            $runParam
        );

        return true;
    }

    /**
     * @param $param
     * @return bool
     * @throws CustomException
     * 执行
     */
    public function run($param){
//        $this->test($param);

        $lotteryPrizeLogService = new LotteryPrizeLogService();
        $lotteryTimes = $lotteryPrizeLogService->getLotteryTimes(
            $param['n8_guid'],
            $param['lottery_id'],
            $param['lottery']['cycle_type']
        );

        if($lotteryTimes >= $param['lottery']['max_times']){
            $tip = "抽奖次数已达上限";
            $cycleTypeMap = Functions::getEnumMapItem(CycleTypeEnum::class, $param['lottery']['cycle_type']);
            if(!empty($cycleTypeMap['next'])){
                $tip .= "，{$cycleTypeMap['next']}再来试试把";
            }

            throw new CustomException([
                'code' => 'LOTTERY_TIME_IS_MAX',
                'message' => $tip,
            ]);
        }

        // 抽取奖品
        $this->drawPrize($param);
        $prize = $this->getPrize();

        // 创建获奖记录
        $data = [
            'n8_guid' => $param['n8_guid'],
            'lottery_id' => $param['lottery_id'],
            'prize_id' => $prize['id'],
            'prize_type' => $prize['prize_type'],
        ];
        unset($param['lottery']['description']);
        unset($param['lottery']['lottery_prizes']);
        $ret = $lotteryPrizeLogService->create($data, $param['lottery'], $prize);
        if(!$ret){
            throw new CustomException([
                'code' => 'CREATE_LOTTERY_PEIZE_LOG_FAIL',
                'message' => '创建获奖记录失败',
            ]);
        }

        return $ret;
    }

    /**
     * @param $param
     * @throws CustomException
     * 测试
     */
    public function test($param){
        $map = [];
        for($i = 0; $i < 10000; $i++){

            // 抽取奖品
            $this->drawPrize($param);
            $prize = $this->getPrize();

            if(!isset($map[$prize['name']])){
                $map[$prize['name']] = 0;
            }

            $map[$prize['name']] += 1;
        }
        dd($map);
    }

    /**
     * @param $param
     * @return mixed|null
     * @throws CustomException
     * 抽取奖品
     */
    public function drawPrize($param){
        $lottery = $this->getCache($param['lottery_id']);

        $lotteryPrizeLogService = new LotteryPrizeLogService();
        $stat = $lotteryPrizeLogService->getLotteryPrizeStat($param['lottery_id']);

        $prizes = [];
        foreach($lottery['lottery_prizes'] as $k => $v){
            // 移除没库存或中奖几率为0奖品
            if($v['total'] == 0 || $v['chance'] == 0){
                continue;
            }

            // 移除没有库存的奖品
            $count = $stat[$v['id']] ?? 0;
            if($v['total'] > 0 && $count >= $v['total']){
                continue;
            }

            $prizes[] = $v;
        }

        if(empty($prizes)){
            throw new CustomException([
                'code' => 'LOTTERY_PRIZES_IS_EMPTY',
                'message' => '奖品已被抽完，下次再来试试把',
            ]);
        }

        $chanceTotal = 0;
        $stairs = [];
        foreach($prizes as $prize){
            $stairs[$chanceTotal] = $prize;
            $chanceTotal += $prize['chance'] * 10000;
        }

        $rnd = mt_rand(0, $chanceTotal);

        krsort($stairs);

        foreach($stairs as $threshold => $prize){
            if($rnd >= $threshold){
                $this->setPrize($prize);
                break;
            }
        }

        if(empty($this->getPrize())){
            throw new CustomException([
                'code' => ResponseCodeEnum::NETWORK_ERROR,
                'message' => '网络繁忙',
                'data' => [
                    'rnd' => $rnd,
                    'stairs' => $stairs,
                ],
                'log' => true,
            ]);
        }

        return true;
    }
}
