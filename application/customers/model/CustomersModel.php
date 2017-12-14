<?php

namespace app\customers\model;

use app\common\model\BasicModel;


/**
 * User: liaoyizhong
 * Date: 2017/12/1/001
 * Time: 15:43
 */
class CustomersModel extends BasicModel
{
    const PROCESSBEFORE = 1;
    const PROCESSING = 2;
    const PROCESSAFTER = 3;

    protected $table = 'customers';

    public function records()
    {
        return $this->hasMany('\app\customers\model\CustomersRecordsModel', 'customers_id', 'id');
    }

    public function residence()
    {
        return $this->hasOne('\app\residences\model\ResidencesModel', 'id', 'residence_id');
    }

    public function design()
    {
        return $this->hasOne('\app\residences\model\ResidencesDesignModel', 'id', 'design_id');
    }
}