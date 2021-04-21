<?php

namespace App\Http\Controllers\Front\UserAction;

use App\Models\UserFollowActionModel;

class FollowActionController extends UserActionController
{


    protected $timeFilterField = 'action_time';


    /**
     * constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setModel(new UserFollowActionModel());
    }

    public function item($item){
        $item->union_user = $item->union_user($item['n8_guid'],$item['channel_id']);
        return $item;
    }


}
