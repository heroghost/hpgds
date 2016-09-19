<?php
/**
 * 极光推送
 * @author 517909399@qq.com
 * @version 20121109
 */

class jpush {
	private $_username;
	private $_appkeys;
	private $_masterSecret;
	
	/**
	 * 构造函数
	 * @param string $username
	 * @param string $password
	 * @param string $appkeys
	 */
	public function __construct($username = '', $appkeys = '', $_masterSecret = '') {
		$this->_username = $username;
		$this->_appkeys = $appkeys;
		$this->_masterSecret = $_masterSecret;
	}
	/**
	 * 模拟post进行url请求
	 * @param string $url
	 * @param string $param
	 */
	public function request_post($url = '', $param = '', $header = '', $userPasswd="") {
		if (empty($url) || empty($param)) {
			return false;
		}
		
		$postUrl = $url;
		$curlPost = $param;
		$ch = curl_init();//初始化curl
		curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
                if(!empty($header)) {
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                }
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查  
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);  // 从证书中检查SSL加密算法是否存在  
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
                curl_setopt($ch,CURLOPT_USERPWD, $userPasswd);
                
		curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
		$data = curl_exec($ch);//运行curl
                
                
		curl_close($ch);
		
		return $data;
	}
	/**
	 * 发送
	 * @param int $sendno 发送编号。由开发者自己维护，标识一次发送请求
	 * @param int $receiver_type 接收者类型。1、指定的 IMEI。此时必须指定 appKeys。2、指定的 tag。3、指定的 alias。4、 对指定 appkey 的所有用户推送消息。
	 * @param string $receiver_value 发送范围值，与 receiver_type相对应。 1、IMEI只支持一个 2、tag 支持多个，使用 "," 间隔。 3、alias 支持多个，使用 "," 间隔。 4、不需要填
	 * @param int $msg_type 发送消息的类型：1、通知 2、自定义消息
	 * @param string $msg_content 发送消息的内容。 与 msg_type 相对应的值
	 * @param string $platform 目标用户终端手机的平台类型，如： android, ios 多个请使用逗号分隔
	 */	
	public function send($sendno = 0, $receiver_type = 1, $receiver_value = '', $msg_type = 1, $msg_content = '', $platform = 'android') {
		//$url = 'http://api.jpush.cn:8800/sendmsg/v2/sendmsg';
		$url = 'http://api.jpush.cn:8800/v2/push';
		
		$param = '';
				
		//$username = $this->_username;
		//$param .= 'username='.$username;
		
		$param .= '&sendno='.$sendno;
		
		$appkeys = $this->_appkeys;	
		$param .= '&app_key='.$appkeys;
		
		$param .= '&receiver_type='.$receiver_type;	
			
		$param .= '&receiver_value='.$receiver_value;
		
		$masterSecret = $this->_masterSecret;
		$verification_code = strtoupper(md5($sendno.$receiver_type.$receiver_value.$masterSecret));		
		$param .= '&verification_code='.$verification_code;	
		
		$param .= '&msg_type='.$msg_type;		
		
		$param .= '&msg_content='.$msg_content;
		
		$param .= '&platform='.$platform;
				
		$res = $this->request_post($url, $param);	
		if ($res === false) {
			return false;
		}	
		$res_arr = json_decode($res, true);		
		
		return $res_arr;
	}
        
        public function sendV3($sendNo, $message, $timeToLive, $isProduction = 0, $platform = 'all', $tag = null, $alias = null, $regId = null ) {
            
            $url = 'https://api.jpush.cn/v3/push';
            
            $params['platform'] = $platform;
            if($tag != null || $alias != null || $regId != null) {
                if($tag != null) {
                    $params['audience']['tag'] = $tag;
                }
                if($alias != null) {
                    $params['audience']['alias'] = $alias;
                }
                if($regId != null) {
                    $params['audience']['registration_id'] = $regId;
                }
            } else {
                $params['audience'] = 'all';
            }
            $params['notification']['alert'] = $message;
            if($sendNo != null || $timeToLive != null) {
                if($sendNo != null) {
                    $params['options']['sendno'] = $sendNo;
                }
                if($timeToLive != null) {
                    $params['options']['time_to_live'] = $timeToLive;
                }
                if($isProduction) {
                    $params['options']['apns_production'] = 'True';
                } else {
                    $params['options']['apns_production'] = 'False';
                }
                       
            } 

            $params = json_encode($params);
            
            
            $appkeys = $this->_appkeys;

            $masterSecret = $this->_masterSecret;
            $userPasswd = $appkeys.':'.$masterSecret;
            $auth = 'Basic '.base64_encode($appkeys.':'.$masterSecret);
            $header = array('Authorization'=>$auth);
            $header['Content-Type'] = 'application/json';

            $res = $this->request_post($url, $params, $header, $userPasswd);
            if ($res === false) {
                return false;
            }
            $res_arr = json_decode($res, true);

            return $res_arr;
        }
	
}
