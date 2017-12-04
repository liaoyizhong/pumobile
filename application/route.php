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
        'regions' => ['region/region'],
        'residences' => ['residences/residences'],
        'customers' => ['customer/customer'],
        'residencesdesigns' => ['residences/ResidencesDesign']
    ],
    'residences/:id/designs' => ['residences/residences/designs',['method'=>'get'],['id'=>'\d+']],
];
