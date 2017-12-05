<?php
/**
 * User: liaoyizhong
 * Date: 2017/12/5/005
 * Time: 11:43
 */

namespace app\customer\logic;

use app\customer\model\CustomerRecordImage;
use app\customer\model\CustomerRecordImage as RecordImage;

class CustomerRecordImageLogic
{
    public function cleanUp($recordId)
    {
        $model = new CustomerRecordImage();
        $list = $model->where('customers_records_id', $recordId)->where('is_delete', CustomerRecordImage::NOTDELETE)->select();
        if (!count($list)) {
            return TRUE;
        }
        $saveParams = [];
        foreach ($list as $key => $value) {
            $value->is_delete = RecordImage::ISDELETE;
            $saveParams[] = $value->getData();
        }

        return $model->saveAll($saveParams);
    }

    public function UpdateAll($params)
    {
        $model = new CustomerRecordImage();
        return $model->saveAll($params);
    }
}