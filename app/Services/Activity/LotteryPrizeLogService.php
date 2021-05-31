<?php

namespace App\Services\Activity;

use App\Common\Enums\CycleTypeEnum;
use App\Common\Enums\ExchangeStatusEnum;
use App\Common\Helpers\Functions;
use App\Common\Services\BaseService;
use App\Enums\PrizeTypeEnum;
use App\Models\LotteryPrizeLogModel;

class LotteryPrizeLogService extends BaseService
{
    /**
     * constructor.
     */
    public function __construct(){
        parent::__construct();
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
}
