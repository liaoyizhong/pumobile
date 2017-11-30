<?php
namespace app\users\validate;
use think\Validate;

/**
 * User: liaoyizhong
 * Date: 2017/11/24/024
 * Time: 9:47
 */

class User extends Validate
{
    protected $rule = [
        'name' => 'require',
        'password' => 'require'
    ];

    protected $message = [
        'name.require' => '缺名字',
        'password.require' => '缺密码'
    ];
}