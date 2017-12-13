<?php
namespace app\common\logic;
use app\common\model\ErrorLogModel;
/**
 * User: liaoyizhong
 * Date: 2017/11/20/020
 * Time: 10:49
 */
class ErrorLogLogic
{
    public function save($msg)
    {
        $model = new ErrorLogModel();
        $model->msg = $msg;
        $model->save();
    }

}