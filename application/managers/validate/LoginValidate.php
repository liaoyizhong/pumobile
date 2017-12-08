<?php
namespace app\managers\validate;
use think\Validate;

/**
 * User: liaoyizhong
 * Date: 2017/12/8/008
 * Time: 9:20
 */

class LoginValidate extends Validate
{
    protected $rule = [
        'phone' => 'require',
        'code' => 'require'
    ];
}