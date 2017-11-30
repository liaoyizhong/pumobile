<?php
/**
 * User: liaoyizhong
 * Date: 2017/11/16/016
 * Time: 15:01
 */

namespace app\residences\model;
use app\common\model\BasicModel;

class ResidencesDesignImagesModel extends BasicModel
{
    protected $table = 'residences_design_images';

    public function image()
    {
        return $this->hasOne('\app\images\model\ImagesModel','image_has_code','has_code');
    }
}