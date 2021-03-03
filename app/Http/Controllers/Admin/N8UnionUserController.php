<?php


namespace App\Http\Controllers\Admin;


use App\Models\N8UnionUserModel;

class N8UnionUserController extends BaseController
{

    protected $defaultOrderBy = 'created_time';
    protected $defaultOrderType = 'desc';



    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new N8UnionUserModel();

        parent::__construct();
    }





}
