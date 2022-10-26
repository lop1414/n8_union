<?php

namespace App\Services;

use App\Common\Services\BaseService;
use App\Models\ReadSignModel;

class ReadSignService extends BaseService
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new ReadSignModel();
    }


    public function saveWithGet($data){
        if(empty($data)) return false;
        return $this->model->firstOrCreate($data);
    }

}
