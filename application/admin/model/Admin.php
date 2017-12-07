<?php
/**
 * author      : Gavin <liyn2007@qq.com>
 * createTime  : 2017/8/18 09:26
 * description :
 */

namespace app\admin\model;


use app\common\model\BaseModel;

class Admin extends BaseModel
{
    // 隐藏字段
    protected $hidden = ['delete_time', 'update_time'];
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    // 插入时自动添加
    protected $insert = ['status' => 1];

    public function getStatusTextAttr($value, $data)
    {
        $statusText = [0 => '禁用', 1 => '正常', 2 => '未验证'];

        return key_exists($data['status'], $statusText) ? $statusText[$data['status']] : '';
    }

    public static function getById($id)
    {
        $admin = self::where('id', '=', $id)
            ->find();

        if (empty($admin)) {
            return '';
        }

        return $admin;
    }

    public function setPasswordAttr($value)
    {
        return password_hash($value, PASSWORD_DEFAULT);
    }
}