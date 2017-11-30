<?php
namespace app\residences\model;
use app\common\model\BasicModel;

/**
 * User: liaoyizhong
 * Date: 2017/11/8/008
 * Time: 14:39
 */

class ResidencesModel extends BasicModel
{
    const ISHIDDEN = 1;
    const NOTHIDDEN = 2;
    protected $table = 'residences';

    public function creator()
    {
        return $this->belongsTo('\app\users\model\UsersModel','creator_id','id');
    }

    public function updator()
    {
        return $this->belongsTo('\app\users\model\UsersModel','updator_id','id');
    }

    public function designs()
    {
        return $this->hasMany('\app\residences\model\ResidencesDesignModel','residences_id','id');
    }
}