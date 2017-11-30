<?php
namespace app\common\services;
use app\common\models\ErrorLogModel;
/**
 * User: liaoyizhong
 * Date: 2017/11/20/020
 * Time: 10:49
 */
class ErrorLogService
{
    public function save($msg)
    {
        $model = new ErrorLogModel();
        $model->msg = $msg;
        $model->save();
    }

    public function get()
    {

    }
}