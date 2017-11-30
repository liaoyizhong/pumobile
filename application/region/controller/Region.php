<?php

namespace app\region\controller;

use \app\common\controller\Basic as BasicController;
use app\common\enums\ResponseCode;
use think\Response;

/**
 * User: liaoyizhong
 * Date: 2017/11/30
 * Time: 10:21
 */
class Region extends BasicController
{
    public function index()
    {
        $service = \think\Loader::model('RegionService', 'service');
        $data = $service->zhongShan();
        if (!$data) {
            return $this->showResponse(ResponseCode::DATA_MISS, '数据不存在');
        }
        return $this->showResponse(ResponseCode::SUCCESS, '', [$data]);
    }
}