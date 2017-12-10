<?php
/**
 * User: liaoyizhong
 * Date: 2017/12/8/008
 * Time: 15:27
 */

namespace app\customers\controller;
use app\common\controller\Basic;
use app\common\enums\HeaderStatus;
use app\common\enums\ResponseCode;
use think\Loader;

class Login extends Basic
{
    public function save()
    {
        $params = $this->getParams(self::METHODPOST);
        $logic = Loader::model('LoginLogic','logic');
        $result = $logic->doLogin($params);
        if(!$result[0]){
            return $this->showResponse(ResponseCode::LOGIC_ERROR,$result[1],[],array('status'=>HeaderStatus::BADREQUEST));
        }
        return $this->showResponse(ResponseCode::SUCCESS,$result[1],$result[2],array('status'=>HeaderStatus::SUCCESS));
    }
}