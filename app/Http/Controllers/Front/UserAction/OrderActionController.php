<?php

namespace App\Http\Controllers\Front\UserAction;

use App\Models\OrderModel;

class OrderActionController extends UserActionController
{

    protected $timeFilterField = 'order_time';

    /**
     * constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $model = new OrderModel();
        $this->setModel($model);
    }


    public function item($item){
        $item->extend;
        $item = $this->model->expandFields($item);
        $item->union_user;
        return $item;
    }

}
