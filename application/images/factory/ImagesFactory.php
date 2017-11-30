<?php
namespace app\images\factory;
/**
 * User: liaoyizhong
 * Date: 2017/11/20/020
 * Time: 18:18
 */

class ImagesFactory
{
    /**
     *  用BasicInterface做限制，用ImagesFactory做类创建，这样就可以快速切换实现类,比如下面的AliServie
     * @return \app\images\services\AliService object
     */
    public static function createImage()
    {
        return \think\Loader::model('\app\images\services\AliService','services');
    }
}