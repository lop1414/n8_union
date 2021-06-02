<?php

namespace App\Services\Activity;

use App\Common\Enums\CycleTypeEnum;
use App\Common\Enums\ExchangeStatusEnum;
use App\Common\Helpers\Functions;
use App\Common\Services\BaseService;
use App\Common\Tools\CustomRedis;
use App\Enums\PrizeTypeEnum;
use App\Models\LotteryPrizeLogModel;
use Illuminate\Support\Facades\DB;

class LotteryPrizeLogService extends BaseService
{
    /**
     * @var string
     * 缓存前缀
     */
    protected $cachePrefix = 'lottery_prize_log_stat';

    /**
     * @var CustomRedis
     * 缓存驱动
     */
    protected $cacheDriver;

    /**
     * constructor.
     */
    public function __construct(){
        parent::__construct();

        $this->cacheDriver = new CustomRedis();
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
     * @param $data
     * @param $lottery
     * @param $lotteryPrize
     * @return bool
     * @throws \App\Common\Tools\CustomException
     * 创建
     */
    public function create($data, $lottery, $lotteryPrize){
        $this->validRule($data, [
            'n8_guid' => 'required',
            'lottery_id' => 'required',
            'prize_id' => 'required',
            'prize_type' => 'required',
        ]);

        $extends = [
            'lottery' => $lottery,
            'lottery_prize' => $lotteryPrize,
        ];

        $exchangeStatus = ExchangeStatusEnum::WAITING;
        if($data['prize_type'] == PrizeTypeEnum::NOTHING){
            $exchangeStatus = ExchangeStatusEnum::SUCCESS;
        }

        $lotteryPrizeLogModel = new LotteryPrizeLogModel();
        $lotteryPrizeLogModel->n8_guid = $data['n8_guid'];
        $lotteryPrizeLogModel->lottery_id = $data['lottery_id'];
        $lotteryPrizeLogModel->prize_id = $data['prize_id'];
        $lotteryPrizeLogModel->prize_type = $data['prize_type'];
        $lotteryPrizeLogModel->exchange_status = $exchangeStatus;
        $lotteryPrizeLogModel->extends = $extends;
        $ret = $lotteryPrizeLogModel->save();

        if(!!$ret){
            // 缓存自增
            $this->cacheDriver->hIncrBy($this->getKey($data['lottery_id']), $data['prize_id'], 1);
        }

        return $ret;
    }

    /**
     * @param $n8Guid
     * @param $lotteryId
     * @param $cycleType
     * @return mixed
     * @throws \App\Common\Tools\CustomException
     * 获取用户抽奖次数
     */
    public function getLotteryTimes($n8Guid, $lotteryId, $cycleType){
        Functions::hasEnum(CycleTypeEnum::class, $cycleType);

        // 获取周期时间范围
        $cycleTimeRange = Functions::getCycleTimeRange($cycleType);

        // 获取时间范围内抽奖次数
        $lotteryPrizeLogModel = new LotteryPrizeLogModel();
        $times = $lotteryPrizeLogModel->whereBetween('created_at', $cycleTimeRange)
            ->where('n8_guid', $n8Guid)
            ->where('lottery_id', $lotteryId)
            ->count();

        return $times;
    }

    /**
     * @param $lotteryId
     * @return array
     * 获取已中奖品统计
     */
    public function getLotteryPrizeStat($lotteryId){
        return $this->getCache($lotteryId);
    }

    /**
     * @param $lotteryId
     * @return array
     * 获取缓存
     */
    public function getCache($lotteryId){
        $stat = $this->cacheDriver->hGetAll($this->getKey($lotteryId));
        if(empty($stat)){
            $stat = $this->setCache($lotteryId);
        }

        return $stat;
    }

    /**
     * @param $lotteryId
     * @return array
     * 设置缓存
     */
    public function setCache($lotteryId){
        $sql = "
            SELECT prize_id, COUNT(*) count FROM lottery_prize_logs
                WHERE lottery_id = {$lotteryId}
                GROUP BY prize_id
        ";
        $items = DB::select($sql);

        $stat = [];
        foreach($items as $item){
            $stat[$item->prize_id] = $item->count;
        }

        // 清除
        $this->cacheDriver->del($this->getKey($lotteryId));

        foreach($stat as $prizeId => $count){
            $this->cacheDriver->hIncrBy($this->getKey($lotteryId), $prizeId, $count);
        }

        return $stat;
    }
}
