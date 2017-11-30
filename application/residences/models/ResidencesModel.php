<?php
namespace app\residences\models;
use app\common\models\BasicModel;

/**
 * User: liaoyizhong
 * Date: 2017/11/8/008
 * Time: 14:39
 */

class ResidencesModel extends BasicModel
{
    protected $table = 'residences';

    public function creator()
    {
        return $this->belongsTo('\app\users\models\UsersModel','creator_id','id');
    }

    public function updator()
    {
        return $this->belongsTo('\app\users\models\UsersModel','updator_id','id');
    }

    public function designs()
    {
        return $this->hasMany('\app\residences\models\ResidencesDesignModel','residences_id','id');
    }
}