<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


require_once 'WeixinBase.class.php';
//下面这些东西都需要换成自己的
// APPID (开户邮件中可查看)
define("APP_ID",  "wx1834026d6576db3a");
// 商户号 (开户邮件中可查看)
define("MCH_ID",  "1296267601");
// 商户支付密钥 (https://pay.weixin.qq.com/index.php/account/api_cert)
define("APP_KEY", "jingqubaoshoppayweixin2015zwwJQB");

define("APP_SECRET", "30f603652cb84d9f736190180bf8f58c");
class WeixinPay extends WeixinBase {

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
    
    public function getPay($productName, $totalFee, $outTradeNo) {
        // get prepay id
        $prepay_id = $this->generatePrepayId($productName, $totalFee, $outTradeNo);
        // re-sign it
        $response = array(
            'appid'     => APP_ID,
            'partnerid' => MCH_ID,
            'prepayid'  => $prepay_id,
            'package'   => 'Sign=WXPay',
            'noncestr'  => $this->generateNonce(),
            'timestamp' => time(),
        );
        $response['sign'] = $this->calculateSign($response, APP_KEY);
        return $response;
    }

    /**
     * Generate a nonce string
     *
     * @link https://pay.weixin.qq.com/wiki/doc/api/app.php?chapter=4_3
     */
    public function generateNonce() {
        return md5(uniqid('', true));
    }
    /**
     * Get a sign string from array using app key
     *
     * @link https://pay.weixin.qq.com/wiki/doc/api/app.php?chapter=4_3
     */
    public function calculateSign($arr, $key) {
        ksort($arr);
        $buff = "";
        foreach ($arr as $k => $v) {
            if ($k != "sign" && $k != "key" && $v != "" && !is_array($v)){
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        return strtoupper(md5($buff . "&key=" . $key));
    }
    /**
     * Get xml from array
     */
     public function getXMLFromArray($arr) {
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml .= sprintf("<%s>%s</%s>", $key, $val, $key);
            } else {
                $xml .= sprintf("<%s><![CDATA[%s]]></%s>", $key, $val, $key);
            }
        }
        $xml .= "</xml>";
        return $xml;
    }
    /**
     * Generate a prepay id
     *
     * @link https://pay.weixin.qq.com/wiki/doc/api/app.php?chapter=9_1
     */
    public function generatePrepayId($productName, $totalFee, $outTradeNo) {
        $params = array(
            'appid'            => APP_ID,
            'mch_id'           => MCH_ID,
            'nonce_str'        => $this->generateNonce(),
            'body'             => $productName,
            'out_trade_no'     => $outTradeNo,
            'total_fee'        => $totalFee,
            'spbill_create_ip' => '120.76.84.221',
            'notify_url'       => 'http://120.76.84.221/api/Order/notify_alipay',
            'trade_type'       => 'APP',
        );
        // add sign
        $params['sign'] = $this->calculateSign($params, APP_KEY);
        // create xml
        $xml = $this->getXMLFromArray($params);
        // send request
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL            => "https://api.mch.weixin.qq.com/pay/unifiedorder",
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => array('Content-Type: text/xml'),
            CURLOPT_POSTFIELDS     => $xml,
        ));
        $result = curl_exec($ch);
        curl_close($ch);
        // get the prepay id from response
        $xml = simplexml_load_string($result);
        return (string)$xml->prepay_id;
    }
    function __destruct() {
        $this->redis->close();
    }
}