<?php


namespace App\Http\Controllers\Admin;


use App\Models\CpChannelModel;


class CpChannelController extends BaseController
{



    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new CpChannelModel();

        parent::__construct();
    }




}
