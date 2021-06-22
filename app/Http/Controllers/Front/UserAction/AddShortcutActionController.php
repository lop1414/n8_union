<?php

namespace App\Http\Controllers\Front\UserAction;

use App\Models\UserShortcutActionModel;

class AddShortcutActionController extends UserActionController
{

    protected $timeFilterField = 'action_time';


    /**
     * constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setModel(new UserShortcutActionModel());
    }



    public function item($item){
        $item->union_user;
        return $item;
    }
}
