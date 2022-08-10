<?php

namespace App\Services\Cp\Channel;

use App\Common\Enums\ProductTypeEnums;

class MbWeChatMiniProgramChannelService extends MbDyMiniProgramChannelService
{



    public function getType(): string
    {
        return ProductTypeEnums::WECHAT_MINI_PROGRAM;
    }



}
