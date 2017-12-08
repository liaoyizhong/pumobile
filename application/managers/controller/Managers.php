<?php
namespace app\managers\controller;
use app\common\controller\Basic;

/**
 * User: liaoyizhong
 * Date: 2017/12/7/007
 * Time: 18:34
 */
class Managers extends Basic
{
    public function read($id)
    {
        $logic = \think\Loader::model('Managers','logic');
        return $logic->get($id);
    }


}