<?php
/**
 * User: liaoyizhong
 * Date: 2017/12/5/005
 * Time: 11:43
 */

namespace app\customers\logic;

use app\customers\model\CustomersRecordsImagesModel;
use app\customers\model\CustomersRecordsImagesModel as RecordImage;

class CustomersRecordsImagesLogic
{
    public function cleanUp($recordId)
    {
        $model = new CustomersRecordsImagesModel();
        $list = $model->where('customers_records_id', $recordId)->where('is_delete', CustomersRecordsImagesModel::NOTDELETE)->select();
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

    public function saveAll($params)
    {
        $model = new CustomersRecordsImagesModel();
        return $model->saveAll($params);
    }
}