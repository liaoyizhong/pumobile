<?php
namespace app\users\models;
use app\common\models\BasicModel;

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
        return $this->hasOne('\app\residences\models\ResidencesModel','creator_id','id');
    }
}