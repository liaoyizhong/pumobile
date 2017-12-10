<?php

namespace app\managers\logic;

use app\common\enums\LoginRole;
use app\common\logic\BasicLogic;
use app\managers\model\ManagersModel;
use think\Cache;

/**
 * User: liaoyizhong
 * Date: 2017/12/8/008
 * Time: 10:23
 */
class LoginLogic extends BasicLogic
{
    /**
     * 管理员短信登录
     * @param $params
     * @return array
     */
    public function doLogin($params)
    {
        //校对用户
        $model = new ManagersModel();
        $model = $model->where('phone', $params['phone'])->find();
        if (!$model) {
            return [FALSE, '帐号不正确', []];
        }

        $cache = Cache::get($params['phone']);
        //验证身份
        if($cache['role'] != LoginRole::ROLEMANAGER){
            return [FALSE, '未被授权的验证码',[]];
        }
        //验证token
        if ($cache['code'] != $params['code']) {
            return [FALSE, "验证码错误", []];
        }

        $token = $this->generateJWT(array("uid" => $model->id, "phone" => $model->phone));
        if ($token) {
            header('token:' . $token);
        }
        $msg = array(
            'user_id' => $model->id,
            'role' => LoginRole::ROLEMANAGER
        );
        if (Cache::set($token, $msg, 7200)) {
            Cache::rm($params['phone']);
            return [TRUE, "登录成功", array('token'=>$token)];
        } else {
            return [FALSE, "登录失败", []];
        }
    }



}