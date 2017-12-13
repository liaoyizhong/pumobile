<?php

namespace app\admin\controller;

use app\common\controller\Basic;
use app\common\enums\HeaderStatus;
use app\common\enums\ResponseCode;
use app\common\service\FrontEndSms;
use app\lib\enum\SmsTemplateEnum;

/**
 * User: liaoyizhong
 * Date: 2017/12/8/008
 * Time: 13:38
 */
class Sms extends Basic
{
    public function save()
    {
        $params = $this->getParams(self::METHODPOST);
        $check = $this->validate($params, 'SmsValidate');
        if ($check !== TRUE) {
            return $this->showResponse(ResponseCode::PARAMS_INVALID, $check, '', array('status' => HeaderStatus::BADREQUEST));
        }
        $sms = new FrontEndSms();
        try {
            $sms->sendCommonSms($params['phone'], SmsTemplateEnum::SMS_FORGET_TEMPLATE_CODE, $params['role']);
            return $this->showResponse(ResponseCode::SUCCESS, '生成成功', [], array('status' => HeaderStatus::SUCCESS));
        } catch (\exception $e) {
            return $this->showResponse(ResponseCode::UNKNOW_ERROR, $e->getMessage(), [], array('status' => HeaderStatus::BADREQUEST));
        }
    }
}