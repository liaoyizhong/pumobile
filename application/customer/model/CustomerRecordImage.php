<?php
/**
 * User: liaoyizhong
 * Date: 2017/12/4/004
 * Time: 15:29
 */

namespace app\customer\model;
use app\common\model\BasicModel;

class CustomerRecordImage extends BasicModel
{
    const ISDELETE = 1;
    const NOTDELETE = 2;

    protected $table = 'customers_records_images';
}