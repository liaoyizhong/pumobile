<?php
/**
 * User: liaoyizhong
 * Date: 2017/12/4/004
 * Time: 15:30
 */

namespace app\customer\logic;

use app\common\logic\BasicLogic;
use app\customer\model\CustomerRecord;

class CustomerRecordLogic extends BasicLogic
{
    public function save($params)
    {
        $model = new CustomerRecord();
        $model->customers_id = $params['customers_id'];
        $model->content = $params['content'];
        return $model->save();
    }
}