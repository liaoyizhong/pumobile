<?php

namespace app\admin\validate;

use app\customers\model\CustomersModel;
use app\home\model\VisitorModel;
use app\managers\model\ManagersModel;
use think\Validate;

/**
 * User: liaoyizhong
 * Date: 2017/12/8/008
 * Time: 9:20
 */
class SmsValidate extends Validate
{
    const TYPEMANAGER = 1;  //管理员
    const TYPECUSTOMER = 2; //客户

    protected $rule = [
        'phone' => 'require',
        'role' => 'require|checkRole'
    ];

    protected function checkRole($value = array(),$rule,$data)
    {
        if($value == self::TYPEMANAGER){
            $model = new ManagersModel();
            $model = $model->where('phone', $data['phone'])->find();

            if (!$model) {
                return '帐号不正确';
            }
            return TRUE;
        }elseif($value == self::TYPECUSTOMER){
            RETURN TRUE;
        }else{
            return '不正确的类型';
        }
    }


}
