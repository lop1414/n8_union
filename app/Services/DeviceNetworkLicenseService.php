<?php

namespace App\Services;

use App\Common\Enums\DeviceBrandEnum;
use App\Common\Helpers\Functions;
use App\Common\Services\ErrorLogService;
use App\Models\DeviceNetworkLicenseModel;

class DeviceNetworkLicenseService
{

    /**
     * @var string[]
     * 各品牌的设备进网许可以申请单位
     */
    private $companies = array(
        [
            'name'  => '华为技术有限公司',
            'brand_enum' => DeviceBrandEnum::HUAWEI,
        ],
        [
            'name'  => '华为终端有限公司',
            'brand_enum' => DeviceBrandEnum::HUAWEI,
        ],
        [
            'name'  => '小米通讯技术有限公司',
            'brand_enum' => DeviceBrandEnum::XIAOMI,
        ],
        [
            'name'  => '深圳市万普拉斯科技有限公司',
            'brand_enum' => DeviceBrandEnum::ONEPLUS, //一加
        ],
        [
            'name'  => 'OPPO广东移动通信有限公司',
            'brand_enum' => DeviceBrandEnum::OPPO,
        ],
        [
            'name'  => '维沃移动通信有限公司',
            'brand_enum' => DeviceBrandEnum::VIVO,
        ],
        [
            'name'  => '东软集团（大连）有限公司',
            'brand_enum' => DeviceBrandEnum::MEIZU, //魅族
        ],
        [
            'name'  => '惠州三星电子有限公司',
            'brand_enum' => DeviceBrandEnum::SAMSUNG,//三星
        ],
        [
            'name'  => 'RealMe重庆移动通信有限公司',
            'brand_enum' => DeviceBrandEnum::REALME, //魅族
        ],
        [
            'name'  => '努比亚技术有限公司',
            'brand_enum' => DeviceBrandEnum::NUBIA, //努比亚
        ],
        [
            'name'  => '青岛海信通信有限公司',
            'brand_enum' => DeviceBrandEnum::HISENSE, //海信
        ],
        [
            'name'  => '南昌黑鲨科技有限公司',
            'brand_enum' => DeviceBrandEnum::BLACKSHARK, //黑鲨
        ]
    );

    /**
     * @return bool
     * @throws \App\Common\Tools\CustomException
     * 同步设备型号
     */
    public function syncDeviceInfo(){
        $endYearDate = intval(date('Y'));
        $yearDate = 2000; //开始年份
        foreach ($this->companies as $item){
            $company = $item['name'];
            while ($yearDate <= $endYearDate){

                $start = $yearDate.'-01-01';
                $end = $yearDate.'-12-31';
                // 按年同步
                $data = $this->apiGetInfo($company,$start,$end);

                var_dump( "{$company} : 年 : {$yearDate} : {$data['total']} ");
                $yearDate += 1;
                if($data['total'] >= 30){
                    $this->syncDeviceInfoByMonth($company,$yearDate);
                    continue;
                }
                $this->saveData($data['records']);
            }
        }


        return true;
    }

    public function apiGetInfo($company,$startDate,$endDate){
        $url = 'https://jwxk.miit.gov.cn/dev-api-20/internetService/CertificateQuery';
        $param = array(
            'equipmentModel' => '',
            'applyOrg'       => $company,
            'pageNo'         => 1,
            'pageSize'       => 100,
            'isphoto'        => 2
        );
        if(empty(!$startDate)){
            $param['startDate'] = $startDate;
            $param['endDate'] = $endDate;
        }

        $ret = file_get_contents($url .'?'. http_build_query($param));
        $result = json_decode($ret, true);
        if(!isset($result['code']) || $result['code'] != 200){
            //无该条纪录
            if($result['code'] == 500){
                return ['records' => [],'total' => 0];
            }
            throw new \Exception('查询失败-'.$result['message'],$result['code']);
        }

        return $result['data'];
    }

    /**
     * @param $company
     * @param $year
     * @throws \App\Common\Tools\CustomException
     * 按月同步
     */
    public function syncDeviceInfoByMonth($company,$year){
        $monthList = Functions::getMonthListByRange([$year.'-01-01',$year.'-12-31']);
        foreach ($monthList as $month){
            $startDate = $month.'-01';
            $endDate = date('Y-m-d',strtotime("{$startDate} +1 month -1 day"));
            $data = $this->apiGetInfo($company,$startDate,$endDate);

            var_dump( "{$company} : 月 : {$month} : {$data['total']} ");

            if($data['total'] >= 30){
                $this->syncDeviceInfoByDay($company,$month);
                continue;
            }
            $this->saveData($data['records']);
        }
    }


    /**
     * @param $company
     * @param $month
     * @throws \App\Common\Tools\CustomException
     * 按天同步
     */
    public function syncDeviceInfoByDay($company,$month){
        $start = $month.'-01';
        $end = date('Y-m-d',strtotime("{$start} +1 month -1 day"));
        $dateList = Functions::getDateListByRange([$start,$end]);
        foreach ($dateList as $date){
            $data = $this->apiGetInfo($company,$date,$date);

            var_dump( "{$company} : 天 : {$month} : {$data['total']} ");

            if($data['total'] >= 30){
                $message = '同步设备信息-当天超过30个';
                $errData = [
                    'company' => $company,
                    'date'    => $date
                ];
                (new ErrorLogService())->create(0,$message,$errData,'DEFAULT');
                dd($message,$errData);
                continue;
            }
            $this->saveData($data['records']);
        }
    }




    public function saveData($data){
        $arr = [];
        foreach ($data as $item){
            $arr[] = [
                'name'           => $item['equipmentName'],
                'model'          => $item['equipmentModel'],
                'apply_org'      => $item['applyOrg'],
                'reg_date'       => $item['regDate'],
                'end_date'       => $item['endDate'],
                'license_no'     => $item['licenseNo']
            ];
        }
        (new DeviceNetworkLicenseModel())->chunkInsertOrUpdate($arr);

    }

}
