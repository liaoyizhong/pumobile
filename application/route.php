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

    'sms-code' => ['admin/sms/save',['method'=>'post']],//登录验证码
    'managers/login' => ['managers/login/save',['method'=>'post']], //管理员登录

    'customers/login' => ['customers/login/save',['method'=>'post']], //客户员登录
    'customers/:id/process' => ['customers/customers/listProcess',['method'=>'get'],['id'=>'\d+']], //我家进度 业务视角指定 客户信息
    'customers/process/' => ['customers/customers/listProcess',['method'=>'get']], //我家进度 业务视角
    'customers/:id/residence' => ['customers/customersRecords/listResidence',['method'=>'get'],['id'=>'\d+']], //装修直播页面
    'customers/residence' => ['customers/customersRecords/listResidence',['method'=>'get'],['id'=>'\d+']], //装修直播页面返回全部

    'residences/:id/designs' => ['residences/residences/designs',['method'=>'get'],['id'=>'\d+']],
    'test-test' => ['test/test/say',['method'=>'post']],//调试
];
