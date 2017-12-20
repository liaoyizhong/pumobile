<?php
namespace app\home\controller;
use app\common\controller\Basic;

/**
 * User: liaoyizhong
 * Date: 2017/12/18/018
 * Time: 17:46
 */

class Visitor extends Basic
{
    public function index()
    {
        $sql = 'select phone from customers group by phone';
        $list = \think\Db::query($sql);
        echo '<pre>';var_dump($list);echo '</pre>';exit();
    }
}