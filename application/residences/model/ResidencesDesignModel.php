<?php
/**
 * User: liaoyizhong
 * Date: 2017/11/16/016
 * Time: 11:50
 */

namespace app\residences\model;
use app\common\model\BasicModel;

class ResidencesDesignModel extends BasicModel
{
    protected $table = 'residences_design';

    public function images()
    {
        return $this->hasMany('\app\residences\model\ResidencesDesignImagesModel','residences_design_id','id');
    }
}