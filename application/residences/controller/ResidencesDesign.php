<?php
/**
 * User: liaoyizhong
 * Date: 2017/12/4/004
 * Time: 9:10
 */

namespace app\residences\controller;

use app\common\controller\Basic;
use app\common\enums\HeaderStatus;
use app\common\enums\ResponseCode;

class ResidencesDesign extends Basic
{
    /**
     * 通过楼盘id查找对应的户型列表
     * @param $id residencesId
     * @return \think\response\Json
     */
    public function read($id)
    {
        $logic = \think\Loader::model('\app\residences\logic\ResidencesLogic','logic');
        $result = $logic->getDesignsMenu($id);
        if(!$result[0]){
            return $this->showResponse(ResponseCode::UNKNOW_ERROR,$result[1],'',array("status"=>HeaderStatus::NOTFOUND));
        }else{
            return $this->showResponse(ResponseCode::SUCCESS,$result[1],$result[2],array("status"=>HeaderStatus::SUCCESS));
        }
    }
}