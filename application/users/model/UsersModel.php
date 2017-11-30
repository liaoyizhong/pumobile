<?php
namespace app\users\model;
use app\common\model\BasicModel;

/**
 * User: liaoyizhong
 * Date: 2017/11/7/007
 * Time: 14:45
 */

class UsersModel extends BasicModel
{
    protected $table = "users";

    public function residences()
    {
        return $this->hasOne('\app\residences\model\ResidencesModel','creator_id','id');
    }
}