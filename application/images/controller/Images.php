<?php

namespace app\images\controller;

use app\common\controller\Basic as BasicController;
use app\common\enums\ResponseCode;
use app\images\factory\ImagesFactory;

/**
 * User: liaoyizhong
 * Date: 2017/11/16/016
 * Time: 14:59
 */
class Images extends BasicController
{
    public function save()
    {
        $image = ImagesFactory::createImage();
        $result = $image->sendImages($_FILES);
        if ($result[0]) {
            $imgUrl = $image->getUrl($result[2]);
            return $this->showResponse(ResponseCode::SUCCESS, $result[1],array("hash_code"=>$result[2],"img_url"=>$imgUrl));
        } else {
            return $this->showResponse(ResponseCode::UNKNOW_ERROR, $result[1]);
        }
    }

    public function read($id)
    {
        $image = ImagesFactory::createImage();
        $result = $image->getImagesUrl($id);
        if ($result[0]) {
            return $this->showResponse(ResponseCode::SUCCESS, $result[1], $result[2]);
        } else {
            return $this->showResponse(ResponseCode::UNKNOW_ERROR, $result[1]);
        }
    }

    /**
     * 提供一个特殊方法直接返回图片的访问地址
     * @param $id
     * @return string
     */
    public function getUrl($id)
    {
        $image = ImagesFactory::createImage();
        $result = $image->getImagesUrl($id);
        if ($result[0]) {
            return $result[1];
        } else {
            return '';
        }
    }
}