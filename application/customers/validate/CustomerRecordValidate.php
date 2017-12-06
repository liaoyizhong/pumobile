<?php
namespace app\customers\validate;
/**
 * User: liaoyizhong
 * Date: 2017/12/4/004
 * Time: 15:42
 */

namespace app\customer\validate;


class CustomerRecordValidate extends \think\Validate
{
    protected $rule = [
        'customers_id' => 'require',
        'content' => 'require',
    ];
}