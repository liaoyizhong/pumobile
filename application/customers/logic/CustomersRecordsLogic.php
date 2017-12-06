<?php
/**
 * User: liaoyizhong
 * Date: 2017/12/4/004
 * Time: 15:30
 */

namespace app\customers\logic;

use app\common\logic\BasicLogic;
use app\customers\model\CustomersRecordsModel;

class CustomersRecordsLogic extends BasicLogic
{
    public function save($params)
    {
        $model = new CustomersRecordsModel();
        $model->customers_id = $params['customers_id'];
        $model->content = $params['content'];
        return $model->save();
    }
}