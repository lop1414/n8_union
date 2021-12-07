<?php

namespace App\Services;

use App\Common\Services\BaseService;
use App\Common\Tools\CustomException;
use App\Models\TestBookGroupModel;
use App\Models\TestBookModel;
use App\Models\TestBookTestBookGroupModel;

class TestBookGroupService extends BaseService
{
    /**
     * constructor.
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * @param $testBookGroupId
     * @param array $testBookIds
     * @return bool
     * @throws CustomException
     * 批量更新
     */
    public function batchUpdate($testBookGroupId, array $testBookIds){
        foreach($testBookIds as $testBookId){
            $this->update($testBookGroupId, $testBookId);
        }
        return true;
    }

    /**
     * @param $testBookGroupId
     * @param $testBookId
     * @return bool
     * @throws CustomException
     * 更新
     */
    public function update($testBookGroupId, $testBookId){
        $testBookGroup = TestBookGroupModel::find($testBookGroupId);
        if(empty($testBookGroup)){
            throw new CustomException([
                'code' => 'NOT_FOUND_TEST_BOOK__GROUP',
                'message' => '找不到该分组',
            ]);
        }

        $testBook = TestBookModel::find($testBookId);
        if(empty($testBook)){
            throw new CustomException([
                'code' => 'NOT_FOUND_TEST_BOOK',
                'message' => '找不到该测试书',
                'data' => [
                    'test_book_id' => $testBookId,
                ],
            ]);
        }

        $testBookTestBookGroup = (new TestBookTestBookGroupModel())
            ->where('test_book_id',$testBookId)
            ->where('test_group_id',$testBookGroupId)
            ->first();

        if(!empty($testBookTestBookGroup)){
            return true;
        }

        $testBookTestBookGroup = new TestBookTestBookGroupModel();
        $testBookTestBookGroup->test_book_id = $testBookId;
        $testBookTestBookGroup->test_group_id = $testBookGroupId;
        $testBookTestBookGroup->save();
        return true;
    }
}
