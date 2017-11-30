<?php
/**
 * User: liaoyizhong
 * Date: 2017/11/16/016
 * Time: 15:36
 */

namespace app\residences\services;
use app\common\services\BasicService;
use app\residences\models\ResidencesDesignImagesModel;

class ResidencesDesignImagesService extends BasicService
{
    public function saveAll($params){
        $model = new ResidencesDesignImagesModel();
        return $model->saveAll($params);
    }
}