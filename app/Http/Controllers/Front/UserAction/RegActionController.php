<?php

namespace App\Http\Controllers\Front\UserAction;

use App\Models\N8UnionUserModel;

class RegActionController extends UserActionController
{

    protected $timeFilterField = 'created_time';


    /**
     * constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setModel( new N8UnionUserModel());
    }



    public function item($item){
        $item->extend;
        return $this->model->expandFields($item);
    }

}
