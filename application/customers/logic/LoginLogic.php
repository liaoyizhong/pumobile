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
use app\home\model\VisitorModel;
use app\managers\model\ManagersModel;
use think\Cache;
use WxUser;

class LoginLogic extends BasicLogic
{
    public function doLogin($params)
    {
        //校对用户
        $model = new VisitorModel();
        $isExist = $model->where('phone', $params['phone'])->find();


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

        //验证码正确以后如果没有用户就进行用户注册
        if(!$isExist){
            $wxModel = new VisitorModel();
            $wxModel->phone = $params['phone'];
            $wxModel->createtime = date('Y-m-d H:i:s',time());
            $wxId = $wxModel->save();
        }else{
            $wxId = $isExist->id;
        }


        $token = $this->generateJWT(array("uid" => $wxId, "phone" => $params['phone']));

        if ($token) {
            header('token:' . $token);
        }
        
        $msg = array(
            'user_id' => $wxId,
            'phone' => $params['phone'],
            'role' => LoginRole::ROLECUSTOMER
        );
        if (Cache::set($token, $msg, 28800)) {
            //删除电话号码的短信验证码
            Cache::rm($params['phone']);
            return [TRUE, "登录成功", array('token'=>$token)];
        } else {
            return [FALSE, "登录失败", []];
        }
    }
}