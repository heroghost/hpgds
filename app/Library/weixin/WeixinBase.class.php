<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


 define("TOKEN", "jingqubao");
define("appid", "wxdcb447934ddf8be6");
define("appsecret", "13a80a67e1d96690bcf13041c2a82eaf");

class WeixinBase {

    private $redis;
    private $rootUrl;

    function __construct() {
//        define("TOKEN", "jingqubao");
//        define("appid","wxdcb447934ddf8be6");
//        define("appsecret","13a80a67e1d96690bcf13041c2a82eaf");
        $this->redis = new redis();
        
        $redisConfig = C('REDIS_ADDRESS');
        $this->redis->connect($redisConfig[0]['ip'], $redisConfig[0]['port']);
        if (!$this->redis->get('access_token')) {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . appid . "&secret=" . appsecret;
            $json = Http::http_request_json($url);
            $data = json_decode($json, true);
            $this->redis->setex('access_token', 7100, $data['access_token']);
        }
        $this->rootUrl = C('ROOT_URL');
    }

    function __destruct() {
        $this->redis->close();
    }
}