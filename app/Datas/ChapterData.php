<?php


namespace App\Datas;


use App\Common\Datas\BaseData;
use App\Models\ChapterModel;

class ChapterData extends BaseData
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
        ['book_id','cp_chapter_id']
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
        parent::__construct(ChapterModel::class);
    }



    public function save($data){
        $where = [
            'book_id'       => $data['book_id'],
            'cp_chapter_id' => $data['cp_chapter_id']
        ];
        $this->setParams($where)->clear();

        return $this->getModel()->updateOrCreate($where, [
                'name'          => $data['name'],
                'seq'           => $data['seq']
            ]
        );
    }

}
