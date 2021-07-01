<?php
namespace App\Http\Controllers\Admin;


use App\Models\BookModel;

class BookController extends BaseController
{

    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new BookModel();

        parent::__construct();
    }

}
