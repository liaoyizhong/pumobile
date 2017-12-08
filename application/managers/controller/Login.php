<?php
namespace app\managers\controller;
use app\common\controller\Basic;
use app\common\enums\HeaderStatus;
use app\common\enums\ResponseCode;
use think\Loader;


/**
 * User: liaoyizhong
 * Date: 2017/12/8/008
 * Time: 9:18
 */

class Login extends Basic
{
    public function save(){
        $params = $this->getParams(self::METHODPOST);
        $check = $this->validate($params, 'LoginValidate');
        if($check  !== TRUE){
            $this->showResponse(ResponseCode::PARAMS_INVALID, $check,'', array('status'=>HeaderStatus::BADREQUEST));
        }
        $logic = Loader::model('LoginLogic','logic');
        $result = $logic->doLogin($params);
        if($result['0']){
            return $this->showResponse(ResponseCode::SUCCESS, $result[1],$result[2], array('status'=>HeaderStatus::SUCCESS));
        }else{
            return $this->showResponse(ResponseCode::UNKNOW_ERROR, $result[1],[], array('status'=>HeaderStatus::BADREQUEST));
        }
    }


}