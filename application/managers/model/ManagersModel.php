<?php
namespace app\managers\model;
use app\common\model\BasicModel;

/**
 * User: liaoyizhong
 * Date: 2017/12/7/007
 * Time: 18:28
 */

class ManagersModel extends BasicModel
{
    const ROLEMANAGER = 1;  //管理员
    const ROLECUSTOMER = 2; //客户

    protected $table = 'managers';
}