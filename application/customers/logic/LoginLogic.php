<?php
/**
 * User: liaoyizhong
 * Date: 2017/12/8/008
 * Time: 15:29
 */

namespace app\customers\logic;


use app\common\enums\LoginRole;
use app\common\logic\BasicLogic;
use app\customers\model\CustomersModel;
use app\managers\model\ManagersModel;
use think\Cache;

class LoginLogic extends BasicLogic
{
    public function doLogin($params)
    {
        //校对用户
        $model = new CustomersModel();
        $isExist = $model->where('phone', $params['phone'])->where('is_delete', '2')->find();
        if(!$isExist){
            return [FALSE, '账号不正确',[]];
        }

        $cache = Cache::get($params['phone']);
        //验证身份
        if(!$cache){
            return [FALSE,'无效code',[]];
        }

        if($cache['role'] != LoginRole::ROLECUSTOMER){
            return [FALSE,'未被授权的验证码',[]];
        }

        //验证token
        if ($cache['code'] != $params['code']) {
            return [FALSE, "验证码错误", []];
        }

        $token = $this->generateJWT(array("uid" => $isExist->id, "phone" => $isExist->phone));

        if ($token) {
            header('token:' . $token);
        }
        
        $msg = array(
            'user_id' => $isExist->id,
            'phone' => $isExist->phone,
            'role' => LoginRole::ROLECUSTOMER
        );
        if (Cache::set($token, $msg, 28800)) {
            Cache::rm($params['phone']);
            return [TRUE, "登录成功", array('token'=>$token)];
        } else {
            return [FALSE, "登录失败", []];
        }
    }
}