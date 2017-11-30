<?php
/**
 * User: liaoyizhong
 * Date: 2017/11/16/016
 * Time: 15:36
 */

namespace app\residences\logic;
use app\common\logic\BasicLogic;
use app\residences\model\ResidencesDesignImagesModel;

class ResidencesDesignImagesLogic extends BasicLogic
{
    const TYPEHOUSE = 1;
    const TYPEHOUSETXT = 'house';
    const TYPEDECORATION = 2;
    const TYPEDECORATIONTXT = 'decoration';

    public function saveAll($params){
        $model = new ResidencesDesignImagesModel();
        return $model->saveAll($params);
    }
}