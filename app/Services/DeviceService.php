<?php

namespace App\Services;


use App\Common\Enums\DeviceBrandEnum;

class DeviceService{


    /**
     * @param string $agent
     * @return string
     * 获取手机型号枚举
     */
     public function getDeviceBrandEnum( $agent = ''){
        $arr = array(
            //苹果
            DeviceBrandEnum::APPLE => [
                '/iPhone\s([^\s|;]+)/i'
            ],
            //华为
            DeviceBrandEnum::HUAWEI => [
                '/Huawei|Honor|H60-|H30-\s([^\s|;]+)/i'
            ],
            //小米
            DeviceBrandEnum::XIAOMI => [
                '/xiaomi|Mi note|mi one\s([^\s|;]+)/i'
            ],
            //OPPO
            DeviceBrandEnum::OPPO => [
                '/OPPO|X9007|X907|X909|R831S|R827T|R821T|R811|R2017|PBEM00|PACM00\s([^\s|;]+)/i'
            ],
            //VIVO
            DeviceBrandEnum::VIVO => [
                '/vivo\s([^\s|;]+)/i'
            ],
            //魅族
            DeviceBrandEnum::MEIZU => [
                '/M045|M032|M355\s([^\s|;]+)/i'
            ],
            //三星
            DeviceBrandEnum::SAMSUNG => [
                '/SAMSUNG|Galaxy|GT-|SCH-|SM-\s([^\s|;]+)/i'
            ],
            //一加
            DeviceBrandEnum::ONEPLUS => [
                '/ONEPLUS\s([^\s|;]+)/i'
            ],
            //红米
            DeviceBrandEnum::HONGMI => [
                '/HM NOTE|HM201\s([^\s|;]+)/i'
            ],
            //酷派
            DeviceBrandEnum::COOLPAD => [
                '/Coolpad|8190Q|5910\s([^\s|;]+)/i'
            ],
            //中兴
            DeviceBrandEnum::ZTE => [
                '/ZTE|X9180|N9180|U9180\s([^\s|;]+)/i'
            ],
            //HTC
            DeviceBrandEnum::HTC => [
                '/HTC|Desire\s([^\s|;]+)/i'
            ],
            //努比亚
            DeviceBrandEnum::NUBIA => [
                '/Nubia|NX50|NX40\s([^\s|;]+)/i'
            ],
            //金立
            DeviceBrandEnum::GIONEE => [
                '/Gionee|GN\s([^\s|;]+)/i'
            ],
            //海信
            DeviceBrandEnum::HISENSE => [
                '/HS-U|HS-E\s([^\s|;]+)/i'
            ],
            //联想
            DeviceBrandEnum::LENOVO => [
                '/Lenove\s([^\s|;]+)/i'
            ],
            //天语
            DeviceBrandEnum::KTOUCH => [
                '/K-Touch\s([^\s|;]+)/i'
            ],
            //朵唯
            DeviceBrandEnum::DOOV => [
                '/DOOV\s([^\s|;]+)/i'
            ],
            //基伍
            DeviceBrandEnum::GFIVE => [
                '/GFIVE\s([^\s|;]+)/i'
            ],
            //诺基亚
            DeviceBrandEnum::NOKIA => [
                '/Nokia\s([^\s|;]+)/i'
            ],
        );

        foreach ($arr as $brandEnum => $regulars){
            foreach ($regulars as $regular){
                if(preg_match($regular, $agent, $regs)){
                    return $brandEnum;
                }
            }
        }

        return DeviceBrandEnum::OTHER;
    }
}


