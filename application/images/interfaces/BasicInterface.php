<?php
namespace app\images\interfaces;
/**
 * User: liaoyizhong
 * Date: 2017/11/21/021
 * Time: 10:52
 */

interface BasicInterface
{
    /**
     * @param $file 要上传的文件信息
     * @return mixed
     */
    public function sendImages($file);

    /**
     * @param $hashCode 图片唯一标识
     * @return string 图片访问地址
     */
    public function getImagesUrl($hashCode);

}