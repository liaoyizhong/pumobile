<?php
/**
 * User: liaoyizhong
 * Date: 2017/12/20/020
 * Time: 9:49
 */

return [

    "wechat"=>[
        "app_id" => "wxe016d95590aa3b88",
        "secret" => "cf4ee5b01efa6583dc8033cb6234ad29",
        "token" => "xiaopujia",
        "aes_key" => "6sIJcIDt5JeMH2MicwIMXM9VMufmm3LNqFhgFCre6eT",
        "log" => [
            "level" => "debug",
            "file" => "/tmp/easywechat.log", // XXX: 绝对路径！！！！
        ],
        "oauth" => [
            "scopes" => ["snsapi_userinfo"],
            "callback" => "http://puwap.lubanfenqi.com/wechats/oauthCallback",
        ],
    ]
/*
    "wechat"=>[
        'app_id' => 'wxcd996178b37a9d22',
        'secret' => '63d38b2adc6971de4818a14b0fc170ad',
        'token'  => 'lbfx',
        'aes_key' => 'CBwoHG5qTiXZ7y8SrrvITyAGrGvMeGwPNRAfNPq6zcV', // 可选
        "log" => [
            "level" => "debug",
            "file" => "/tmp/easywechat.log", // XXX: 绝对路径！！！！
        ],
        "oauth" => [
            "scopes" => ["snsapi_userinfo"],
            "callback" => "http://puwap.lubanfenqi.com/wechats/oauthCallback",
        ],
    ]*/
];