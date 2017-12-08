<?php

namespace app\admin\validate;

use app\customers\controller\CustomersRecords;
use app\customers\model\CustomersRecordsModel;
use app\managers\model\ManagersModel;
use think\Validate;

/**
 * User: liaoyizhong
 * Date: 2017/12/8/008
 * Time: 9:20
 */
class SmsValidate extends Validate
{
    const TYPEMANAGER = 1;
    const TYPECUSTOMER = 2;

    protected $rule = [
        'phone' => 'require',
        'type' => 'checkRole'
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
            $model = new CustomersRecordsModel();
            $isExist = $model->where('phone', $data['phone'])->where('is_delete', '2')->find();
            if(!$isExist){
                return '账号不正确';
            }
            RETURN TRUE;
        }else{
            return '不正确的类型';
        }
    }


}
