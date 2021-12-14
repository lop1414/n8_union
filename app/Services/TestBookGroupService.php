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
     * @param string $testBookId
     * @param array $testBookGroupIds
     * @return bool
     * @throws CustomException
     * 分配测试书籍
     */
    public function assignTestBook($testBookId, array $testBookGroupIds){
        $testBook = TestBookModel::find($testBookId);
        if(empty($testBook)){
            throw new CustomException([
                'code' => 'NOT_FOUND_TEST_BOOK',
                'message' => '找不到该测试书',
                'data' => ['test_book_id' => $testBookId,],
            ]);
        }

        //删除
        (new TestBookTestBookGroupModel())->where('test_book_id',$testBookId)->delete();

        foreach($testBookGroupIds as $testBookGroupId){
            $this->check($testBookGroupId);
            $info = new TestBookTestBookGroupModel();
            $info->test_book_id = $testBookId;
            $info->test_book_group_id = $testBookGroupId;
            $info->save();
        }
        return true;
    }



    /**
     * @param string $testBookGroupId
     * @param array $adminIds
     * @return bool
     * @throws CustomException
     * 分配管理员
     */
    public function assignAdminUser( $testBookGroupId,array $adminIds){

        $this->check($testBookGroupId);
        //删除
        (new TestBookGroupAdminUserModel())->where('test_book_group_id',$testBookGroupId)->delete();

        foreach ($adminIds as $adminId){
            $info = new TestBookGroupAdminUserModel();
            $info->admin_id = $adminId;
            $info->test_book_group_id = $testBookGroupId;
            $info->save();
        }

        return true;
    }


}
