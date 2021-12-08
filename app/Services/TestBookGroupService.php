<?php

namespace App\Services;

use App\Common\Services\BaseService;
use App\Common\Tools\CustomException;
use App\Models\TestBookGroupAdminUserModel;
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


    public function check($testBookGroupId){
        $testBookGroup = TestBookGroupModel::find($testBookGroupId);
        if(empty($testBookGroup)){
            throw new CustomException([
                'code' => 'NOT_FOUND_TEST_BOOK__GROUP',
                'message' => '找不到该分组',
            ]);
        }
    }


    /**
     * @param array $testBookIds
     * @param array $testBookGroupIds
     * @return bool
     * @throws CustomException
     * 分配测试书籍
     */
    public function assignTestBook(array $testBookIds, array $testBookGroupIds){

        foreach($testBookGroupIds as $testBookGroupId){
            $this->check($testBookGroupId);

            foreach ($testBookIds as $testBookId){
                $testBook = TestBookModel::find($testBookId);
                if(empty($testBook)){
                    throw new CustomException([
                        'code' => 'NOT_FOUND_TEST_BOOK',
                        'message' => '找不到该测试书',
                        'data' => ['test_book_id' => $testBookId,],
                    ]);
                }

                (new TestBookTestBookGroupModel())
                    ->insertOrUpdate([
                        'test_book_id' => $testBookId,
                        'test_book_group_id' => $testBookGroupId,
                    ]);
            }
        }
        return true;
    }



    /**
     * @param array $adminIds
     * @param array $testBookGroupIds
     * @return bool
     * @throws CustomException
     * 分配管理员
     */
    public function assignAdminUser(array $adminIds, array $testBookGroupIds){

        foreach($testBookGroupIds as $testBookGroupId){
            $this->check($testBookGroupId);

            foreach ($adminIds as $adminId){

                (new TestBookGroupAdminUserModel())
                    ->insertOrUpdate([
                        'admin_id' => $adminId,
                        'test_book_group_id' => $testBookGroupId
                    ]);
            }
        }
        return true;
    }


}
