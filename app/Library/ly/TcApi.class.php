<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TcApi
 *
 * @author dhp
 */
require_once 'ApiAuthUtil.class.php';
class TcApi {
    const version = '20111128102912';
    const accountId = '147cfc72-b042-4faa-b44f-51c28675d6ef';
    const password = '42510acb5abe6ca7';
    const allianceId = 91040;
    const callBackUrl = "http://v2test.jingqubao.com/w3g/Hd/callback";
    
    
    public function __construct($options = array()) { 
        //$this->CI =& get_instance();
    }

    public function concat_string($array) {
        $arg  = "";
        while (list ($key, $val) = each ($array)) {
            $arg.=$key."=".$val."&";
        }
        $arg = substr($arg,0,count($arg)-2); //去掉最后一个&字符
        return $arg;
    }

    /**
     * 对数组排序
     * $array 排序前的数组
     * return 排序后的数组
     */
    public function arg_sort($array) {
        ksort($array);
        reset($array);
        return $array;
    }

    /**
     * 实现多种字符编码方式
     * $input 需要编码的字符串
     * $_output_charset 输出的编码格式
     * $_input_charset 输入的编码格式
     * return 编码后的字符串
     */
    public function charset_encode($input,$_output_charset ,$_input_charset) {
        $output = "";
        if(!isset($_output_charset))$_output_charset = $_input_charset;
        if($_input_charset == $_output_charset || $input ==null ) {
            $output = $input;
        } elseif (function_exists("mb_convert_encoding")) {
            $output = mb_convert_encoding($input,$_output_charset,$_input_charset);
        } elseif(function_exists("iconv")) {
            $output = iconv($_input_charset,$_output_charset,$input);
        } else die("sorry, you have no libs support for charset change.");
        return $output;
    }

    /**
     * 实现多种字符解码方式
     * $input 需要解码的字符串
     * $_output_charset 输出的解码格式
     * $_input_charset 输入的解码格式
     * return 解码后的字符串
     */
    public function charset_decode($input,$_input_charset ,$_output_charset) {
        $output = "";
        if(!isset($_input_charset) )$_input_charset = $_input_charset ;
        if($_input_charset == $_output_charset || $input ==null ) {
            $output = $input;
        } elseif (function_exists("mb_convert_encoding")) {
            $output = mb_convert_encoding($input,$_output_charset,$_input_charset);
        } elseif(function_exists("iconv")) {
            $output = iconv($_input_charset,$_output_charset,$input);
        } else die("sorry, you have no libs support for charset changes.");
        return $output;
    }


    /**
    ** 获取同程网接口数据
    */
    public function s($server_url, $serviceName, $paramArr, $returnArray=true){
        require_once 'XML2Array.php';
        //$server_url = 'http://tcopenapi.17usoft.com/Handlers/General/AdministrativeDivisionsHandler.ashx';	//接口地址
        $paramArr['clientIp'] = $paramArr['clientIp']? $paramArr['clientIp'] : '115.28.22.109';

        $version = self::version;							//接口协议版本号，详见接口协议文档
        $accountID = self::accountId;	//API帐户ID(小写)，待申请审批通过后发
        $accountKey = self::password;		//API帐户密钥，待申请审批通过后发放
        //$serviceName = 'DemoFunction';							//调用接口的方法名称
        $currentMS =  (int)(microtime()*1000); 					//当前时间的毫秒
        $reqTime = date("Y-m-d H:i:s").".".$currentMS;			//当前时间到毫秒
        $arr = array('Version'=>$version,
                'AccountID' => $accountID,      
                'ServiceName' => $serviceName,
                'ReqTime' => $reqTime
        );
        $sort_array  = $this->arg_sort($arr);
        $arg = $this->concat_string($sort_array);
        $digitalSign = md5($arg.$accountKey); //数字签名

        //body中的请求参数
        $params = array();
        foreach($paramArr as $k=>$v){
            $params[] = "<$k>". $v ."</$k>";
        }

        //将$xml_data字符串中的param1节点拿去，即可看到少传参数返回的错误信息显示
        $xml_data = '<?xml version="1.0" encoding="utf-8"?>
        <request>
          <header>
            <version>'.$version.'</version>
            <accountID>'.$accountID.'</accountID>   
            <serviceName>'.$serviceName.'</serviceName>
            <digitalSign>'.$digitalSign.'</digitalSign>
            <reqTime>'.$reqTime.'</reqTime>
          </header>
          <body>
            '. implode('', $params) .'
          </body>
        </request>';

        /*************************************************************
         * 下一行代码视运行环境的字符集设置，决定是否启用
         *************************************************************/
        //$xml_data = $this->charset_encode($xml_data,'GBK','UTF-8');

        $header = array();
        $header[] = "Content-type: text/xml";	//定义content-type为xml
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $server_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_data);
        $response = curl_exec($ch);
        if(curl_errno($ch))
        {
            print curl_error($ch);
        }
        curl_close($ch);
        //print_r($response);

        /*************************************************************
         * 下一行代码视运行环境的字符集设置，决定是否启用
         *************************************************************/
        //$response = $this->charset_decode($response,'UTF-8','GBK');

        //header("Content-type: text/xml");
        return $returnArray? reset(XML2Array::createArray($this->fixXML($response))) : $this->fixXML($response);
    }

    public function fixXML($xml){
        $xml = preg_replace("|<!\-\-[\s\S]+?\-\->|", "", $xml);
        $xml = preg_replace("|[\r\n]|", "", $xml);
        return $xml;
    }

    //景区接口
    public function scenery($serviceName, $paramArr, $returnArray=true){
        return $this->s('http://tcopenapi.17usoft.com/handlers/scenery/queryhandler.ashx', $serviceName, $paramArr, $returnArray);
    }
    
    public function tcPay($desc,$name,$num,$serialId,$amount) {
        $currentMS =  (int)(microtime()*1000); 					//当前时间的毫秒
        $reqTime = date("Y-m-d H:i:s").".".$currentMS;			//当前时间到毫秒
        $apiAuthUtil = new ApiAuthUtil(
                'Scenery',
                'Pay',
                'MobileWapPay',
                self::accountId,
                self::password,
                $reqTime,
                self::allianceId
                );
        
        $url = $apiAuthUtil->getApiURL("http://tcopenapi.17usoft.com/Scenery/Pay/MobileWapPay");
        $callBackurl = self::callBackUrl;
        $payData = "body={$desc}&Subject={$name}&quantity={$num}&serialId={$serialId}&amount={$amount}&callBakUrl={$callBackurl}";
        
        $header = array();
        $header[] = "Content-type: application/x-www-form-urlencoded";	//定义content-type为xml
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payData);
        $result = curl_exec($ch);
        if(curl_errno($ch))
        {
            print curl_error($ch);
        }
        curl_close($ch);
        return $result;
    }

}