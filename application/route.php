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
    //有增删查改动作的资源全部用restful
    '__rest__'=>[
        'regions' => ['region/region'], //区域
        'residences' => ['residences/residences'], //楼盘
        'customers' => ['customers/customers'], //客户信息
        'customers-records' => ['customers/CustomersRecords'], //直播信息
        'residencesdesigns' => ['residences/ResidencesDesign'], //设计户型
        'images' => 'images/images', //图片
    ],
    'sms-code' => ['admin/Sms/save',['method'=>'post']],//登录验证码
    'customers/login' => ['customers/login/save',['method'=>'post']], //客户员登录
    'managers/login' => ['managers/login/save',['method'=>'post']], //管理员登录

    'residences/:id/designs' => ['residences/residences/designs',['method'=>'get'],['id'=>'\d+']],
    'sms' => ['test/test/smsTest',['method'=>'get'],['id'=>'\d+']]
];
