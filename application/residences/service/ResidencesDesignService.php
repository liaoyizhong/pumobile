<?php
namespace app\residences\service;
/**
 * User: liaoyizhong
 * Date: 2017/12/14/014
 * Time: 16:39
 */

class ResidencesDesignService
{
    public function checkExists($id, $residencesId)
    {
        $model = new \app\residences\model\ResidencesDesignModel();
        $row = $model->where('id',$id)->where('residences_id',$residencesId)->find();
        return $row;
    }
}