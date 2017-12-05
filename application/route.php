<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    'login/check' => ['users/login/check',['method'=>'post']],
    '__rest__'=>[
        'regions' => ['region/region'], //区域
        'residences' => ['residences/residences'], //楼盘
        'customers' => ['customer/customer'], //客户信息
        'customers-records' => ['customer/CustomerRecord'], //直播信息
        'residencesdesigns' => ['residences/ResidencesDesign'], //设计户型
        'images' => 'images/images' //图片
    ],
    'residences/:id/designs' => ['residences/residences/designs',['method'=>'get'],['id'=>'\d+']],
    'sms' => ['test/test/smsTest',['method'=>'get'],['id'=>'\d+']]
];
