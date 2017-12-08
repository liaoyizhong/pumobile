<?php
/**
 * User: liaoyizhong
 * Date: 2017/12/8/008
 * Time: 15:29
 */

namespace app\customers\logic;


use app\common\logic\BasicLogic;
use app\customers\controller\CustomersRecords;

class LoginLogic extends BasicLogic
{
    public function doLogin($params)
    {
        $model = new CustomersRecords();
        $isExist = $model->where('phone', $params['phone'])->where('is_delete', '2')->find();
        if(!$isExist){
            return [FALSE, '账号不正确'];
        }
    }
}