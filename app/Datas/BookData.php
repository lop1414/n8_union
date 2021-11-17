<?php


namespace App\Datas;


use App\Common\Datas\BaseData;
use App\Models\BookModel;

class BookData extends BaseData
{

    /**
     * @var bool
     * 缓存开关
     */
    protected $cacheSwitch = true;


    /**
     * @var array
     * 字段
     */
    protected $fields = [];


    /**
     * @var array
     * 唯一键数组
     */
    protected $uniqueKeys = [
        ['cp_type','cp_book_id']
    ];


    /**
     * @var int
     * 缓存有效期
     */
    protected $ttl = 60*60*24;


    /**
     * constructor.
     */
    public function __construct(){
        parent::__construct(BookModel::class);
    }



    public function save($data){

        $this->setParams(['cp_type' => $data['cp_type'],'cp_book_id' => $data['cp_book_id']])->clear();

        return $this->model->updateOrCreate(
            [
                'cp_type'    => $data['cp_type'],
                'cp_book_id' => $data['cp_book_id']
            ],
            [
                'name'          => $data['name'],
                'author_name'   => $data['author_name'],
                'all_words'     => $data['all_words'],
                'update_time'   => $data['update_time']
            ]
        )->toArray();
    }

}
