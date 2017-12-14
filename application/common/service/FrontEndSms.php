<?php
/**
 * author      : Gavin <liyn2007@qq.com>
 * createTime  : 2017/9/7 15:14
 * description :
 */

namespace app\common\service;


use app\common\enums\LoginRole;
use app\lib\enum\SmsTemplateEnum;

class FrontEndSms extends Sms
{
    protected $role = LoginRole::ROLEMANAGER;
    /**
     * 发送短信
     *
     * @param $phoneNumbers
     * @return array
     */
    public function sendCommonSms($phoneNumbers, $template_code = SmsTemplateEnum::SMS_REGISTER_TEMPLATE_CODE)
    {
        $check = $this->verifyCode($phoneNumbers);
        if(!$check[0]){
            return [FALSE,$check[1]];
        }

        $code = $this->createVerifyCode(); // 生成code
        $templateParam = [
            'code' => $code,
            'product' => '小噗家'

        ];

        // 插入短信日志
        $result = $this->sendSms($template_code, $phoneNumbers, $templateParam);
        return $result;
    }

    public function setRole($value)
    {
        $this->role = $value;
    }

    public function getRole()
    {
        return $this->role;
    }
}