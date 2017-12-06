<?php
namespace app\customers\validate;
/**
 * User: liaoyizhong
 * Date: 2017/12/1/001
 * Time: 16:54
 */


class CustomerValidate extends \think\Validate
{
    protected $rule = [
        'region_id' => 'require',
        'residences_id' => 'require',
        'design_id' => 'require',
        'house_num' => 'require',
        'family_name' => 'require',
        'name' => 'require',
        'sex' => 'require',
        'starttime' => 'require',
        'endtime' => 'require',
    ];

    protected $message = [

    ];

}