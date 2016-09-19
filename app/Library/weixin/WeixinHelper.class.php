<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of WeixinHelper
 *
 * @author dhp
 */

 define("TOKEN", "jingqubao");
define("appid", "wxdcb447934ddf8be6");
define("appsecret", "13a80a67e1d96690bcf13041c2a82eaf");

class WeixinHelper {

    private $redis;
    private $rootUrl;

    function __construct() {
//        define("TOKEN", "jingqubao");
//        define("appid","wxdcb447934ddf8be6");
//        define("appsecret","13a80a67e1d96690bcf13041c2a82eaf");
        $this->redis = new redis();
        
        $redisConfig = C('REDIS_ADDRESS');
        $this->redis->connect($redisConfig[0]['ip'], $redisConfig[0]['port']);
        if (!$this->redis->get('qr_access_token')) {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . appid . "&secret=" . appsecret;
            $json = Http::http_request_json($url);
            $data = json_decode($json, true);
            $this->redis->setex('qr_access_token', 7100, $data['access_token']);
        }
        $this->rootUrl = C('ROOT_URL');
    }

    function __destruct() {
        $this->redis->close();
    }
    
    public function check($get) {
        if (isset($get['echostr'])) {        //----验证签名
            $echoStr = $get["echostr"];
            $signature = $get["signature"];
            $timestamp = $get["timestamp"];
            $nonce = $get["nonce"];
            //$token = TOKEN;
            $tmpArr = array(TOKEN, $timestamp, $nonce);
            sort($tmpArr);
            $tmpStr = implode($tmpArr);
            $tmpStr = sha1($tmpStr);
            if ($tmpStr == $signature) {
                echo $echoStr;
                return 1;
            }
            return 2;
        }
        return 0;
    }

    //curl请求url
    public function send($openId, $articles) {
        //$content = "您的景区宝账号为：\n" . $userInfo['nickname'] . "\n密码为：\n" . $password;

        $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' . $this->redis->get('qr_access_token');
        $data = $this->toImageAndText($openId, $articles);
                
        $res = Http::http_request_json($url, $data);
        file_put_contents('/tmp/weixinsend.log', var_export($res, TRUE), FILE_APPEND);
    }
    
    private function toText($openId, $content) {
        $data = '{"touser":"' . $openId . '","msgtype":"text","text":{"content":"' . $content . '"}}';
        return $data;
    }
    
    private function toImage($openId, $mediaId) {
        $data = '{"touser":"' . $openId . '","msgtype":"image","image":{"media_id":"' . $mediaId . '"}}';
        return $data;
    }
    
    private function toAudio($openId, $mediaId) {
        $data = '{"touser":"' . $openId . '","msgtype":"voice","voice":{"media_id":"' . $mediaId . '"}}';
        return $data;
    }
    
    private function toVideo($openId, $mediaId, $title, $description) {
        $data = '{"touser":"' . $openId . '","msgtype":"video","video":{"media_id":"' . $mediaId . '","thumb_media_id":"' . $mediaId . '","title":"' . $title . '","description":"' . $description . '"}}';
        return $data;
    }
    
    //[{"title":"HappyDay","description":"IsReallyAHappyDay","url":"URL","picurl":"PIC_URL"},{"title":"HappyDay","description":"IsReallyAHappyDay","url":"URL","picurl":"PIC_URL"}]
    private function toImageAndText($openId, $articles) {
        $title = $articles[0]['title'];
        $description = $articles[0]['description'];
        $articles[0]['title'] = '{title}';
        $articles[0]['description'] = '{description}';
        $weixinStr = json_encode($articles);
        $weixinStr = str_replace('{title}', $title, $weixinStr);
        $weixinStr = str_replace('{description}', $description, $weixinStr);
        //var_dump($weixinStr);exit;
        $data = '{"touser":"'.$openId.'","msgtype":"news","news":{"articles":'.$weixinStr.'}}';
        return $data;
    }
    
    public function getTicket() {
//        define("TOKEN", "jingqubao");
//        define("appid","wxdcb447934ddf8be6");
//        define("appsecret","13a80a67e1d96690bcf13041c2a82eaf");
        //获取十字路口二维码
//        $redis = new redis();
//        $redis->connect('127.0.0.1', 6379);
        if($this->redis->get('ticket')) {
            return $this->redis->get('ticket');
        }
        
        if (!$this->redis->get('qr_access_token')) {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . appid . "&secret=" . appsecret;
            $json = Http::http_request_json($url);
            $data = json_decode($json, true);
            $this->redis->setex('qr_access_token', 6100, $data['access_token']);
        }
        $access_token = $this->redis->get('qr_access_token');
        //echo $access_token;die();
        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$access_token."&type=jsapi";

        $json = Http::http_request_json($url);
        $res = json_decode($json, true);
        if($res['errcode'] !== 0) {
            $this->redis->del('qr_access_token');
        }

        $this->redis->setex('ticket', 7100, $res['ticket']);
        $this->redis->close();
        return $res['ticket'];
    }
    
    public function generateQrcodeUrl($crossId, $type=3) {
//        
//        //获取十字路口二维码
//        //$redis = new redis();
//        $this->redis->connect('127.0.0.1', 6379);
//        if (!$this->redis->get('access_token')) {
//            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . appid . "&secret=" . appsecret;
//            $json = Http::http_request_json($url);
//            $data = json_decode($json, true);
//            $this->redis->setex('access_token', 7100, $data['access_token']);
//        }
//        $access_token = $redis->get('access_token');
//        $this->redis->close();
//        //echo $access_token;die();
//        $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=" . $access_token;
//
//        $arr = array("action_name" => "QR_LIMIT_SCENE",
//            "action_info" => array(
//                "scene" => array("scene_id" => $type . $crossId)
//            )
//        );
//        $json = Http::http_request_json($url, json_encode($arr));
//        $res = json_decode($json, true);

        $code = $this->getWeixinCode($crossId, $type);
        return 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$code;

    }
    
    public function getPureCode($sceneid) {
        $appId = C('WEIXIN')['APP_ID'];
        $appSecret = C('WEIXIN')['APP_SECRET'];
        //获取景点二维码
        
        $redisConfig = C('REDIS_ADDRESS');
        $this->redis->connect($redisConfig[0]['ip'], $redisConfig[0]['port']);
        if (!$this->redis->get('qr_access_token')) {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $appId . "&secret=" . $appSecret;
            $json = Http::http_request_json($url);
            $data = json_decode($json, true);
            $this->redis->setex('qr_access_token', 7100, $data['access_token']);
        }
        $access_token = $this->redis->get('qr_access_token');
        $this->redis->close();
        //echo $access_token;die();
        $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=" . $access_token;

        $arr = array("action_name" => "QR_LIMIT_SCENE",
            "action_info" => array(
                "scene" => array("scene_id" => $sceneid)
            )
        );
        $json = Http::http_request_json($url, json_encode($arr));
        $res = json_decode($json, true);
        file_put_contents('/tmp/qrcode.log', $json."\n", FILE_APPEND);
        return $res['ticket'];
    }
    
    public function getWeixinCode($scenicId, $type=2) {
        $appId = C('WEIXIN')['APP_ID'];
        $appSecret = C('WEIXIN')['APP_SECRET'];
        //获取景点二维码
        
        $redisConfig = C('REDIS_ADDRESS');
        $this->redis->connect($redisConfig[0]['ip'], $redisConfig[0]['port']);
        if (!$this->redis->get('qr_access_token')) {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $appId . "&secret=" . $appSecret;
            $json = Http::http_request_json($url);
            $data = json_decode($json, true);
            $this->redis->setex('qr_access_token', 7100, $data['access_token']);
        }
        $access_token = $this->redis->get('qr_access_token');
        $this->redis->close();
        //echo $access_token;die();
        $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=" . $access_token;

        $arr = array("action_name" => "QR_LIMIT_SCENE",
            "action_info" => array(
                "scene" => array("scene_id" => $type . $scenicId)
            )
        );
        $json = Http::http_request_json($url, json_encode($arr));
        $res = json_decode($json, true);
        return $res['ticket'];
    }
    
    /**
     * 微信模板消息=》消息通知
     * @param  $msg = array("touid","title","url","content")
     */
    public function wxReply($msg) {
        //$msg['touid'] = 202;
        //$msg['title'] = 'ceshiceshi';
        //$msg['url'] = 'http://www.baidu.com';
        //$msg['content'] ='haha';
        //$userInfo = model('User')->getUserInfo($msg['touid']);
        //$userInfo = $this->db->select('wx_id,username')->where('id', $msg['touid'])->get('user')->row();
        $wx_id = $msg['touid'];//$userInfo['import_id'];
        
        file_put_contents('/var/hpduan/weixin.log', $wx_id, FILE_APPEND);
        if ($wx_id == '') {
            return;
        }
        $userName = $msg['uname'];
        $template_id = "Py-PsaQHCEhbLfF8ulGmGxCMmNPsBtvupxr73hvQDb4";

        $data = array('touser' => $wx_id,
            'template_id' => $template_id,
            'url' => $msg['url'],
            'topcolor' => "#ffffff",
            'data' => array(
                'first' => array('value' => $userName,
                    'color' => '#0000ff'),
                'keyword1' => array('value' => $msg['title'],
                    'color' => '#0000ff'),
                'keyword2' => array('value' => date('Y-m-d H:i'),
                    'color' => '#0000ff'),
                'remark' => array('value' => $msg['content'],
                    'color' => '#0000ff'),
            )
        );
        $this->send_template_message($data);
    }
    
    public function parsingSemantic($msg, $city='北京') {
        require_once ADDON_PATH.'/library/weixin/SemanticHelper.class.php';
        $semantic = new SemanticHelper();
        return $semantic->parse($msg);
//        if (!$this->redis->get('access_token')) {
//            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . appid . "&secret=" . appsecret;
//            $json = Http::http_request_json($url);
//            $data = json_decode($json, true);
//            $this->redis->setex('access_token', 7100, $data['access_token']);
//        }
//        $access_token = $this->redis->get('access_token');
//        //echo $access_token;die();
//        $url = "https://api.weixin.qq.com/semantic/semproxy/search?access_token=".$access_token."";
//        
//        $data['query'] = $msg;
//        $data['city'] = $city;
//        $data['category']  = 'travel';
//        $data['appid'] = appid;
//
//        $json = Http::http_request_json($url,$data);
//        $res = json_decode($json, true);
//        file_put_contents('/var/hpduan/testyy.txt', $res, FILE_APPEND);
    }

    private function send_template_message($data) {
//        define("TOKEN", "jingqubao");
//        define("appid","wxdcb447934ddf8be6");
//        define("appsecret","13a80a67e1d96690bcf13041c2a82eaf");
        $redis = new redis();
        
        $redisConfig = C('REDIS_ADDRESS');
        $redis->connect($redisConfig[0]['ip'], $redisConfig[0]['port']);
        $access_token = $redis->get('qr_access_token');
        if (!$access_token) {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . appid . "&secret=" . appsecret;
            $json = Http::http_request_json($url);
            $data = json_decode($json, true);
            $redis->setex('qr_access_token', 7100, $data['access_token']);
            $access_token = $data['access_token'];
        }
        
        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=" . $access_token;

        $res = Http::http_request_json($url, json_encode($data));
        $redis->close();
        file_put_contents('/var/hpduan/weixin.log', var_export($res, TRUE), FILE_APPEND);
        return json_decode($res, TRUE);
    }

//    private function http_request_json($url, $data = null) {
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
//        if (!empty($data)) {
//            curl_setopt($ch, CURLOPT_POST, 1);
//            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
//        }
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        $result = curl_exec($ch);
//        curl_close($ch);
//        return $result;
//    }
    
    //接收事件消息
    private function receiveEvent($object)
    {
        $content = "";
        switch ($object->Event) {
            case "subscribe":
                //--账号注册绑定
                $openId = $object->FromUserName;

                $res = model('User')->getUserInfoByImportId($openId, null);
                file_put_contents('/var/hpduan/qrcode_content', $openId."\n", FILE_APPEND);
                file_put_contents('/var/hpduan/qrcode_content', var_export($res, TRUE)."\n", FILE_APPEND);
                $uid = $res['uid'];
                
                if (empty($res)) {
                    $password = 'jingqubao' . rand(1, 99);
                    $salt = rand(1000, 9999);
                    
                    $redisConfig = C('REDIS_ADDRESS');
                    $this->redis->connect($redisConfig[0]['ip'], $redisConfig[0]['port']);
                    $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $this->redis->get('access_token') . '&openid=' . $openId . '&lang=zh_CN';
                    $userStr = Http::http_request_json($url);
                    $userInfo = json_decode($userStr, true);
                    //($name, $nick, $sex, $head, $openId, $password,$salt)
                    $this->addUser($userInfo['nickname'],
                            $userInfo['nickname'],
                            $userInfo['nickname'],
                            $userInfo['sex'] == 1 ? 1 : 2,
                            $userInfo['headimgurl'],
                            $openId,
                            $password,
                            $salt
                            );
                    $this->redis->close();
                }
//                    $password = 'jingqubao' . rand(1, 99);
//                    $salt = random_string('alnum', 8);
//                    $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $this->redis->get('access_token') . '&openid=' . $openId . '&lang=zh_CN';
//                    $userInfo = json_decode(Http::http_request_json($url), true);
//                    if (isset($userInfo['nickname'])) {
////                        $email = 'wx' . time() . rand(100, 999) . '@jingqubao.com';
////                        $insert = array(
////                            'username' => $userInfo['nickname'],
////                            'user_type' => 0,
////                            'address' => $userInfo['province'] . '*' . $userInfo['city'],
////                            'password' => md5(md5($password) . $salt),
////                            'salt' => $salt,
////                            'sex' => $userInfo['sex'],
////                            'email' => $email,
////                            'addtime' => time(),
////                            'photo' => $userInfo['headimgurl'],
////                            'wx_id' => $openId
////                        );
////                        $insert_sql = "INSERT INTO `ts_user` (`username`, `user_type`, `password`, `salt`, `sex`, `email`, `addtime`, `photo`, `wx_id`) VALUES ('" . $insert['username'] . "','" . $insert['user_type'] . "','" . $insert['password'] . "','" . $insert['salt'] . "','" . $insert['sex'] . "','" . $insert['email'] . "','" . $insert['addtime'] . "','" . $insert['photo'] . "','" . $insert['wx_id'] . "')";
////                        $this->db->query($insert_sql);
////                        $uid = $this->db->insert_id();
//                        
//                        $user = $this->addUser($userInfo['nickname'], $userInfo['nickname'], $userInfo['sex'], $userInfo['headimgurl'], $openId, $password,$salt);
//                        $email = $user['email'];
//                        $uid = $user['uid'];
//                        
//                        $content = "您的景区宝账号为：\n" . $userInfo['nickname'] . "\n密码为：\n" . $password;
//
//                        $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' . $this->redis->get('access_token');
//                        $data = '{"touser":"' . $openId . '","msgtype":"text","text":{"content":"' . $content . '"}}';
//                        file_put_contents("/var/hpduan/weixin_scan.log", "*****************************************\n", FILE_APPEND);
//                        file_put_contents("/var/hpduan/weixin_scan.log", $openId . "\n", FILE_APPEND);
//                        file_put_contents("/var/hpduan/weixin_scan.log", $userInfo['nickname'] . "\n", FILE_APPEND);
//                        $res = Http::http_request_json($url, $data);
//                    }
//                }

                $content = "Hi，你终于来了！\n全国景区语音讲解、地图导航、游玩贴士，<a href='http://v2.jingqubao.com/app/app.html'>戳我就有</a>我们还有旅行公会群，里面集结了爱玩爱旅行的小伙伴。一起来疯吧，和旅行达人做朋友，跟大家分享你的旅行故事，就在这里😊\n ";

                //带参数二维码
                if (!empty($object->EventKey)) {
                    $eventKey = str_replace("qrscene_", "", $object->EventKey);
                    
                    $ticket = D('wx_ticket')->where(array('id'=>$eventKey))->find();
                    
                    $type = $ticket['type']; //substr($eventKey, 0, 1);
                    $id = $ticket['rid'];//substr($eventKey, 1);
                    $content = array();

                    model('QrcodeVinfo')->add(array(
                        'type'=>$type==1?2:1,
                        'rid'=>$id,
                        'uid'=>$uid,
                        'vTime'=>time(),
                        'ip'=>''
                    ));
                    
                    file_put_contents("/var/hpduan/qrcode_scan.log", "Event Key:" . $eventKey . "\n", FILE_APPEND);
                    if ($type == 1) {
                        //给用户添加群组

//                        $selectSql = "SELECT `rid` FROM `yw_scenic_spots` WHERE `id` = " . $id;
//                        $cidArray = $this->db->query($selectSql)->row_array();
//                        $cid = $cidArray['rid'];
//                        $selectSql = "SELECT `id` FROM `yw_region_group` WHERE `cid` = " . $cid . " ORDER BY id ASC LIMIT 1";
//                        $gidArray = $this->db->query($selectSql)->row_array();
//                        $gid = $gidArray['id'];
//                        if (!($gid > 0)) {
//                            $this->db->query("INSERT INTO `yw_region_group` (`cid` , `name`) VALUES ('" . $id . "' , '默认分组')");
//                            $gid = $this->db->insert_id();
//                        }
//
//                        if ($uid > 0) {
//                            $cc = $this->db->query("SELECT * FROM `yw_user_group` WHERE `uid` = " . $uid . " AND `gid`= " . $gid)->row_array();
//                            if (empty($cc)) {
//                                $this->db->query("INSERT INTO `yw_user_group` (`uid` , `rename` ,`gid` , `rid`, `addtime`) VALUES ( " . $uid . ", '' ," . $gid . " , " . $cid . " , " . time() . ")");
//                            }
//                        }
                        //$this->db->query("INSERT INTO `yw_scenic_spots` (`sid` , `type` , `add_time` ,`isSao`) VALUES ('".$id."' , '4' , '".time()."' ,'1')");
//                        $info = $this->db->select('id,scenic_spots_name,brief,photo')->get_where('scenic_spots', array('id' => $id))->result();
                        $info = model('Spot')->getSimpleInfo($id);
                        $photo = $info['photo'];
                        if (strpos($photo, "qiniu") === false) {
                            $photo = "http://jingqubao.com/" . $photo;
                        }
                        $targetUrl = urlencode(U('w3g/Scenic/index',array('rid'=>$id,'type'=>2)));
                        $baseUrl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxdcb447934ddf8be6&redirect_uri=" . $targetUrl . "&response_type=code&scope=snsapi_base&state=1#wechat_redirect";
                        $content[] = array("Title" => $info['scenic_spots_name'], "Description" => $info['brief'], "PicUrl" => $photo, "Url" => $baseUrl);
                    }
                    if ($type == 2) {
                        //给用户添加群组

//                        $selectSql = "SELECT `id` FROM `yw_region_group` WHERE `cid` = " . $id . " ORDER BY id ASC LIMIT 1";
//                        $gidArray = $this->db->query($selectSql)->row_array();
//                        $gid = $gidArray['id'];
//                        if (!isset($gid)) {
//                            $this->db->query("INSERT INTO `yw_region_group` (`cid` , `name`) VALUES ('" . $id . "' , '默认分组')");
//                            $gid = $this->db->insert_id();
//                        }
//                        if ($uid > 0) {
//
//                            $cc = $this->db->query("SELECT * FROM `yw_user_group` WHERE `uid` = " . $uid . " AND `gid`= " . $gid)->row_array();
//
//                            if (empty($cc)) {
//                                $this->db->query("INSERT INTO `yw_user_group` (`uid` , `rename` ,`gid`, `rid` , `addtime`) VALUES ( " . $uid . ", '' ," . $gid . "," . $id . " , " . time() . ")");
//                            }
//                        }

//                        $info = $this->db->select('id,scenic_region_name,brief,photo')->get_where('scenic_region', array('id' => $id))->result();
                        $info = model('Scenic')->getSimpleInfo($id);
                        $photo = $info['photo'];
                        if (strpos($photo, "qiniu") === false) {
                            $photo = "http://jingqubao.com/" . $photo;
                        }
                        $targetUrl = urlencode(U('w3g/Scenic/index',array('rid'=>$id,'type'=>1)));
                        $baseUrl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxdcb447934ddf8be6&redirect_uri=" . $targetUrl . "&response_type=code&scope=snsapi_base&state=1#wechat_redirect";

                        $content[] = array("Title" => $info['scenic_region_name'], "Description" => $info['brief'], "PicUrl" => $photo, "Url" => $baseUrl); //"Url"=>"http://wap.jingqubao.com/region/".$info[0]->id
                    }
                    if ($type == 3) {//十字路口
//                        $cross = $this->db->query('select spots_id from maps_scenic_code where marker_id=' . $id)->row_array();
                        $cross = model('MapsScenicCode')->getInfo($id);
                        $spotIds = explode(",", $cross['spots_id']);
                        foreach ($spotIds as $sid) {
                            if ($sid != null) {
                                $spotId = $sid;
                            }
                        }
                        if ($spotId != null) {
                            $info = model('Spot')->getSimpleInfo($spotId);//$this->db->select('id,brief,photo')->get_where('scenic_spots', array('id' => $spotId))->result();
                            $photo = $info['photo'];
                        }
                        $targetUrl = urlencode(U('w3g/Scenic/cross',array('cross_id'=>$id)));
                        $baseUrl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxdcb447934ddf8be6&redirect_uri=" . $targetUrl . "&response_type=code&scope=snsapi_base&state=21#wechat_redirect";
                        if (strpos($photo, "qiniu") === false) {
                            $photo = "http://jingqubao.com/" . $photo;
                        }
                        $content[] = array("Title" => '十字路口', "Description" => '点击查看附近景点', "PicUrl" => $photo, "Url" => $baseUrl); //"http://wap.jingqubao.com/region/".$info[0]->id
                    }
                    if ($type == 4) {//广场,临时性代码
                        //$info = $this->db->select('id,scenic_region_name,brief,photo')->get_where('scenic_region',array('id'=>$id))->result();
                        $photo = 'http://7u2psp.com2.z0.glb.qiniucdn.com/20150501gcact.png';
                        $targetUrl = urlencode(U('w3g/Square/square',array('scenic_id'=>$id)));
                        $baseUrl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxdcb447934ddf8be6&redirect_uri=" . $targetUrl . "&response_type=code&scope=snsapi_base&state=21#wechat_redirect";

                        $content[] = array("Title" => '广场页', "Description" => '点击查看广场热度', "PicUrl" => $photo, "Url" => $baseUrl); //"http://wap.jingqubao.com/region/".$info[0]->id
                    }
                    if ($type == 5) {//安徽活动
                        //$info = $this->db->select('id,scenic_region_name,brief,photo')->get_where('scenic_region',array('id'=>$id))->result();
                        $photo = 'http://7u2psp.com2.z0.glb.qiniucdn.com/20150501ahact.png';
                        $targetUrl = urlencode(U('w3g/Public/act',array('act_id'=>$id)));
                        $baseUrl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxdcb447934ddf8be6&redirect_uri=" . $targetUrl . "&response_type=code&scope=snsapi_base&state=21#wechat_redirect";

                        $content[] = array("Title" => '安徽黄山景区宝活动', "Description" => '点击抽奖', "PicUrl" => $photo, "Url" => $baseUrl); //"http://wap.jingqubao.com/region/".$info[0]->id
                    }
                    if ($type == 6) {//南海子活动,临时性代码
                        //$info = $this->db->select('id,scenic_region_name,brief,photo')->get_where('scenic_region',array('id'=>$id))->result();
                        $photo = 'http://7u2psp.com2.z0.glb.qiniucdn.com/20150501nhzact.png';
                        $targetUrl = urlencode(U('w3g/Public/act_nhz',array('scenic_id'=>$id)));
                        $baseUrl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxdcb447934ddf8be6&redirect_uri=" . $targetUrl . "&response_type=code&scope=snsapi_base&state=21#wechat_redirect";

                        $content[] = array("Title" => '南海子景区宝活动', "Description" => '点击抽奖', "PicUrl" => $photo, "Url" => $baseUrl); //"http://wap.jingqubao.com/region/".$info[0]->id
                    }if ($type == 9) {//活动
                        tsload(APPS_PATH.'/admin/Lib/Action/QRcodeAction.class.php');
                        $result=QRcodeAction::getQRcode($type,$id);
                        //$info = $this->db->select('id,scenic_region_name,brief,photo')->get_where('scenic_region',array('id'=>$id))->result();
                        $photo = $result['thumb'];
                        $targetUrl = urlencode($result['url']);
                        $baseUrl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxdcb447934ddf8be6&redirect_uri=" . $targetUrl . "&response_type=code&scope=snsapi_base&state=21#wechat_redirect";
                        if($result){
                            $content[] = array("Title" => $result['activity']['name'], "Description" => '参与活动', "PicUrl" => $photo, "Url" => $baseUrl); //"http://wap.jingqubao.com/region/".$info[0]->id

                        }else{
                            $content[] = array("Title" => '景区宝活动', "Description" => '参与活动', "PicUrl" => $photo, "Url" => $baseUrl); //"http://wap.jingqubao.com/region/".$info[0]->id
                        }
                    }

                }
                model('Tongji')->add_tj($type==2?$id:"",
                        $type==1?$id:"",
                        $uid,
                        "",
                        $type==3?"cross_reg":"qrcode_reg",
                        $targetUrl,
                        "w3g",
                        $type."_".$id,
                        $type==3?"cross_reg":"qrcode_reg");
                break;
            case "unsubscribe":
                $content = "取消关注";
                break;
            case "SCAN":
                //推送不同场景
                //$content = "扫描场景 ".$object->EventKey;
                $openId = $object->FromUserName;
//                $sql = "SELECT `id` FROM (`yw_user`) WHERE `wx_id` = '" . $openId . "'";
//                $res = $this->db->query($sql)->row_array();
                
                $res = model('User')->getUserInfoByImportId("".$openId."");
                $uid = $res['uid'];
                $eventKey = str_replace("qrscene_", "", $object->EventKey);
//                $type = substr($eventKey, 0, 1);
//                $id = substr($eventKey, 1);
                $content = array();
                $ticket = D('wx_ticket')->where(array('id'=>$eventKey))->find();
                    
                $type = $ticket['type']; //substr($eventKey, 0, 1);
                $id = $ticket['rid']; 
                //error_log("eventKey:".$eventKey.PHP_EOL , 3 , "log.log");

                file_put_contents("/var/hpduan/weixin_scan.log", $openId . "\n", FILE_APPEND);
                file_put_contents("/var/hpduan/qrcode_scan.log", "Event Key:" . $eventKey . "\n", FILE_APPEND);

                
                model('QrcodeVinfo')->add(array(
                    'type' => $type==1?2:1,
                    'rid' => $id,
                    'uid' => $uid,
                    'vTime' => time(),
                    'ip' => ''
                ));
                if ($type == 1) {  //--景点				
                    //给用户添加群组
////                    $selectSql = "SELECT `rid` FROM `yw_scenic_spots` WHERE `id` = " . $id;
////                    $cidArray = $this->db->query($selectSql)->row_array();
//                    $cidArray = model('Spot')->getSimpleInfo($id);
//                    $cid = $cidArray['rid'];
//                    $selectSql = "SELECT `id` FROM `yw_region_group` WHERE `cid` = " . $cid . " ORDER BY id ASC LIMIT 1";
//                    $gidArray = $this->db->query($selectSql)->row_array();
//                    $gid = $gidArray['id'];
////
////
////					if(!isset($gid)){
////						$this->db->query("INSERT INTO `yw_region_group` (`cid` , `name`) VALUES ('".$id."' , '默认分组')");
////						$gid = $this->db->insert_id();
////					}
////					if($uid>0){
////						$cc = $this->db->query("SELECT * FROM `yw_user_group` WHERE `uid` = ".$uid ." AND `gid`= ".$gid )->row_array();
////						//error_log("INSERT INTO `yw_user_group` (`uid` , `rename` ,`gid` , `rid`, `addtime`) VALUES ( ".$uid.", '' ,".$gid." , ".$cid." , ".time().")" , 3 , 'log.log');
////						if(empty($cc)){
////							$this->db->query("INSERT INTO `yw_user_group` (`uid` , `rename` ,`gid` , `rid`, `addtime`) VALUES ( ".$uid.", '' ,".$gid." , ".$cid." , ".time().")");
////						}
////					}
//                    //$this->db->query("INSERT INTO ` ` (`sid` , `type` , `add_time` ,`isSao`) VALUES ('".$id."' , '4' , '".time()."' ,'1')");
                    $info = model('Spot')->getSimpleInfo($id);//$this->db->select('id,scenic_spots_name,brief,photo')->get_where('scenic_spots', array('id' => $id))->result();
                    //error_log(PHP_EOL."http://wap.jingqubao.com/region/".$cid.'/'.$info[0]->id.PHP_EOL ,3 , "log.log");
                    $photo = $info['photo'];
                    if (strpos($photo, "qiniu") === false) {
                        $photo = "http://jingqubao.com/" . $photo;
                    }
                    $targetUrl = urlencode(U('w3g/Scenic/index',array('rid'=>$id,'type'=>2)));
                    $baseUrl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxdcb447934ddf8be6&redirect_uri=" . $targetUrl . "&response_type=code&scope=snsapi_base&state=21#wechat_redirect";
                    $content[] = array("Title" => $info['scenic_spots_name'], "Description" => $info['brief'], "PicUrl" => $photo, "Url" => $baseUrl); //"http://wap.jingqubao.com/region/".$cid.'/'.$info[0]->id
                }
                if ($type == 2) {  //--景区
                    //给用户添加群组
//					$selectSql = "SELECT `id` FROM `yw_region_group` WHERE `cid` = ". $id ." ORDER BY id ASC LIMIT 1";
//					$gidArray = $this->db->query($selectSql)->row_array();
//
//					$gid = $gidArray['id'];
//					if(!isset($gid)){
//						$this->db->query("INSERT INTO `yw_region_group` (`cid` , `name`) VALUES ('".$id."' , '默认分组')");
//						$gid = $this->db->insert_id();
//					}
//					if($uid > 0){
//
//						$cc = $this->db->query("SELECT * FROM `yw_user_group` WHERE `uid` = ".$uid ." AND `gid`= ".$gid )->row_array();
//
//						if(empty($cc)){
//							$this->db->query("INSERT INTO `yw_user_group` (`uid` , `rename` ,`gid`, `rid` , `addtime`) VALUES ( ".$uid.", '' ,".$gid.",".$id." , ".time().")");
//						}
//					}
                    $info = model('Scenic')->getSimpleInfo($id);//$this->db->select('id,scenic_region_name,brief,photo')->get_where('scenic_region', array('id' => $id))->result();
                    
                    $photo = $info['photo'];
                    $targetUrl = urlencode(U('w3g/Scenic/index',array('rid'=>$id,'type'=>1)));
                    $baseUrl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxdcb447934ddf8be6&redirect_uri=" . $targetUrl . "&response_type=code&scope=snsapi_base&state=21#wechat_redirect";
                    if (strpos($photo, "qiniu") === false) {
                        $photo = "http://jingqubao.com/" . $photo;
                    }
                    
                    $content[] = array("Title" => $info['scenic_region_name'], "Description" => $info['brief'], "PicUrl" => $photo, "Url" => $baseUrl); //"http://wap.jingqubao.com/region/".$info[0]->id
                }
                if ($type == 3) {//十字路口
                    $cross = model('MapsScenicCode')->getInfo($id);//$this->db->query('select spots_id from maps_scenic_code where marker_id=' . $id)->row_array();
                    
                    $spotIds = explode(",", $cross['spots_id']);
                    file_put_contents("/var/hpduan/weixin_scan.log", $id.'_1'.$cross['spots_id'].'1' . "\n", FILE_APPEND);
                    foreach ($spotIds as $sid) {
                        if ($sid != null) {
                            $spotId = $sid;
                        }
                    }
                    if ($spotId != null) {
                        $info = model('Spot')->getSimpleInfo($spotId);//$this->db->select('id,brief,photo')->get_where('scenic_spots', array('id' => $spotId))->result();
                        $photo = $info['album'][0]['cover'];
                    }
                    file_put_contents("/var/hpduan/weixin_scan.log", $spotId . "\n", FILE_APPEND);
                    $targetUrl = urlencode(U('w3g/Scenic/cross',array('cross_id'=>$id)));
                    $baseUrl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxdcb447934ddf8be6&redirect_uri=" . $targetUrl . "&response_type=code&scope=snsapi_base&state=21#wechat_redirect";
                    if (strpos($photo, "qiniu") === false) {
                        $photo = "http://jingqubao.com/" . $photo;
                    }
                    $content[] = array("Title" => '十字路口', "Description" => '点击查看附近景点', "PicUrl" => $photo, "Url" => $baseUrl); //"http://wap.jingqubao.com/region/".$info[0]->id
                }
                if ($type == 4) {//广场,临时性代码
                    //$info = $this->db->select('id,scenic_region_name,brief,photo')->get_where('scenic_region',array('id'=>$id))->result();
                    $photo = 'http://7u2psp.com2.z0.glb.qiniucdn.com/20150501gcact.png';
                    $targetUrl = urlencode(U('w3g/Square/square',array('scenic_id'=>$id)));
                    $baseUrl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxdcb447934ddf8be6&redirect_uri=" . $targetUrl . "&response_type=code&scope=snsapi_base&state=21#wechat_redirect";

                    $content[] = array("Title" => '广场页', "Description" => '点击查看广场热度', "PicUrl" => $photo, "Url" => $baseUrl); //"http://wap.jingqubao.com/region/".$info[0]->id
                }
                if ($type == 5) {//安徽活动
                    //$info = $this->db->select('id,scenic_region_name,brief,photo')->get_where('scenic_region',array('id'=>$id))->result();
                    $photo = 'http://7u2psp.com2.z0.glb.qiniucdn.com/20150501ahact.png';
                    $targetUrl = urlencode(U('w3g/Public/act',array('act_id'=>$id)));
                    $baseUrl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxdcb447934ddf8be6&redirect_uri=" . $targetUrl . "&response_type=code&scope=snsapi_base&state=21#wechat_redirect";

                    $content[] = array("Title" => '安徽黄山景区宝活动', "Description" => '点击抽奖', "PicUrl" => $photo, "Url" => $baseUrl); //"http://wap.jingqubao.com/region/".$info[0]->id
                }
                if ($type == 6) {//南海子活动,临时性代码
                    //$info = $this->db->select('id,scenic_region_name,brief,photo')->get_where('scenic_region',array('id'=>$id))->result();
                    $photo = 'http://7u2psp.com2.z0.glb.qiniucdn.com/20150501nhzact.png';
                    $targetUrl = urlencode(U('w3g/Public/act_nhz',array('act_id'=>1,'scenic_id'=>$id)));
                    $baseUrl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxdcb447934ddf8be6&redirect_uri=" . $targetUrl . "&response_type=code&scope=snsapi_base&state=21#wechat_redirect";

                    $content[] = array("Title" => '南海子景区宝活动', "Description" => '点击抽奖', "PicUrl" => $photo, "Url" => $baseUrl); //"http://wap.jingqubao.com/region/".$info[0]->id
                }
                if (empty($content)) {
                    $content = "欢迎回到景区宝景区服务号，我们将为您提供景区景点介绍，景区内部导览，景区实时游客分布查询，景区周边及本地精品消费推荐，景区内部交友和活动发布等服务，为您提供旅行中一站式服务体验。 ";
                }

                model('Tongji')->add_tj($type==2?$id:"",
                        $type==1?$id:"",
                        $uid,
                        "",
                        $type==3?"cross_scan":"qrcode_scan",
                        $targetUrl,
                        "w3g",
                        $type."_".$id, 
                        $type==3?"cross_scan":"qrcode_scan");
                break;
            case "CLICK":
                file_put_contents('/tmp/weixinmenu.log', $object->EventKey);
                switch ($object->EventKey) {
                    case "kefu":
                        $result = $this->transmitService($object);
                        
                        return $result;
                        break;
                    case "COMPANY":
                        $content = array();
                        $content[] = array("Title" => "多图文1标题", "Description" => "", "PicUrl" => "http://discuz.comli.com/weixin/weather/icon/cartoon.jpg", "Url" => "http://m.cnblogs.com/?u=txw1958");
                        break;
                    default:
                        $content = "点击菜单：" . $object->EventKey;
                        break;
                }
                break;
            case "LOCATION":
                $content = "上传位置：纬度 " . $object->Latitude . ";经度 " . $object->Longitude;
                break;
            case "VIEW":
                file_put_contents('/tmp/weixinmenu.log', $object->EventKey);
                $content = "跳转链接 " . $object->EventKey;
                break;
            case "MASSSENDJOBFINISH":
                $content = "消息ID：" . $object->MsgID . "，结果：" . $object->Status . "，粉丝数：" . $object->TotalCount . "，过滤：" . $object->FilterCount . "，发送成功：" . $object->SentCount . "，发送失败：" . $object->ErrorCount;
                break;
            default:
                $content = "receive a new event: " . $object->Event;
                break;
        }
        if (is_array($content)) {

            if (isset($content[0])) {
                $result = $this->transmitNews($object, $content);
            } else if (isset($content['MusicUrl'])) {
                $result = $this->transmitMusic($object, $content);
            }
        } else {
            $result = $this->transmitText($object, $content);
        }

        return $result;
    }
    
    private function addUser($name, $nick, $sex, $head, $openId, $password,$salt) {
        //$salt = rand(1000, 9999);
        $rand = rand(1, 9);
        //当人比较多时，这种方式容易冲突
        $mobile = time() . $rand;
        $type = 'Weixin';
        $user = array(
            'login' => $name . $type, //$user_info['type'].$user_info['name'],
            'password' => $password,
            'login_salt' => $salt,
            'uname' => $nick . $type,
            'email' => 'default' . time() . $salt . '@jingqubao.com',
            'sex' => $sex,
            'location' => '',
            'is_audit' => 1,
            'is_active' => 1,
            'is_init' => 1,
            'ctime' => time(),
            'identity' => 1,
            'api_key' => $type,
            'domain' => null,
            'province' => null,
            'city' => null,
            'area' => null,
            'reg_ip' => null,
            'lang' => 'zh-cn',
            'timezone' => 'PRC',
            'is_del' => 0,
            'first_letter' => null,
            'intro' => '',
            'last_login_time' => time(),
            'last_feed_id' => null,
            'last_post_time' => time(),
            'search_key' => null,
            'invite_code' => null,
            'import_id' => $openId,
            'feed_email_time' => null,
            'send_email_time' => null,
            'mobile' => $mobile,
            'cover' => "http://7u2psp.com2.z0.glb.qiniucdn.com/255B6D5AA2F2947EB565FEAD34ABA98A1.jpg",
            'photo' => $head,
            'region_count' => 0,
            'province_count' => 0,
            'city_count' => 0,
            'user_money' => 0,
            'frozen_money' => 0
        );
        $uid = model('User')->addUser($user);
        $user['uid'] = $uid;
        
        $savedata['oauth_token'] = getOAuthToken($uid);
        $savedata['oauth_token_secret'] = getOAuthTokenSecret();
        $savedata['uid'] = $uid;
        $savedata['type'] = 'location';
        //$savedata = array_merge($savedata, $data);
        M('login')->add($savedata);

        return $user;
    }
    
    public function receive($postObj) {
        $RX_TYPE = trim($postObj->MsgType);
        file_put_contents('/mnt/hpduan/weixin_message.log', $RX_TYPE."\n", FILE_APPEND);
        //消息类型分离
        switch ($RX_TYPE) {
            case "event":
                $result = $this->receiveEvent($postObj);
                break;
            case "text":
                $result = $this->receiveText($postObj);
                break;
            case "image":
                $result = $this->receiveImage($postObj);
                break;
            case "location":
                $result = $this->receiveLocation($postObj);
                break;
            case "voice":
                $result = $this->receiveVoice($postObj);
                break;
            case "video":
                $result = $this->receiveVideo($postObj);
                break;
            case "link":
                $result = $this->receiveLink($postObj);
                break;
            default:
                $result = "unknown msg type: " . $RX_TYPE;
                break;
        }
        //$this->logger("T ".$result);
        echo $result;
    }
    
    //接收文本消息
    private function receiveText($object)
    {
        $keyword = trim($object->Content);
        file_put_contents('/mnt/hpduan/weixinword.log', $keyword."\n", FILE_APPEND);
        //多客服人工回复模式
        if (strstr($keyword, "客服") || strstr($keyword, "你43太太好") || strstr($keyword, "在发65448吗")){
            $result = $this->transmitService($object);
        }
        //自动回复模式
        else{
            if (strstr($keyword, "授权1")){
                $content = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxdcb447934ddf8be6&redirect_uri=http://wap.jingqubao.com/sqtest&response_type=code&scope=snsapi_base&state=123#wechat_redirect";
            }else if (strstr($keyword, "授权测试")){
                $content = array();
                $content[] = array("Title"=>"单图文标题",  "Description"=>"单图文内容\nhaha", "PicUrl"=>"http://discuz.comli.com/weixin/weather/icon/cartoon.jpg", "Url" =>"https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxdcb447934ddf8be6&redirect_uri=http://wap.jingqubao.com/sqtest&response_type=code&scope=snsapi_base&state=123#wechat_redirect");
            }else if (strstr($keyword, "单图文本8546文")){
                $content = array();
                $content[] = array("Title"=>"单图文标题",  "Description"=>"单图文内容\nhaha", "PicUrl"=>"http://discuz.comli.com/weixin/weather/icon/cartoon.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
            }else if (strstr($keyword, "图文本8546文") || strstr($keyword, "多图8546文")){
                $content = array();
                $content[] = array("Title"=>"多图文11标题", "Description"=>"多图文1标题多图文1标题多图文1标题", "PicUrl"=>"http://discuz.comli.com/weixin/weather/icon/cartoon.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
                $content[] = array("Title"=>"多图文2标题", "Description"=>"多图文2标题多图文2标题多图文2标题", "PicUrl"=>"http://d.hiphotos.bdimg.com/wisegame/pic/item/f3529822720e0cf3ac9f1ada0846f21fbe09aaa3.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
                $content[] = array("Title"=>"多图文3标题", "Description"=>"多图文3标题多图文3标题多图文3标题", "PicUrl"=>"http://g.hiphotos.bdimg.com/wisegame/pic/item/18cb0a46f21fbe090d338acc6a600c338644adfd.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
            }else if (strstr($keyword, "音文本8546乐")){
                $content = array();
                $content = array("Title"=>"最炫民族风", "Description"=>"歌手：凤凰传奇", "MusicUrl"=>"http://121.199.4.61/music/zxmzf.mp3", "HQMusicUrl"=>"http://121.199.4.61/music/zxmzf.mp3");
            }else{
				$openId = $object->FromUserName;
                $msg=$object->Content;
                
                $str = preg_replace('/\s/','',$msg);
                if(trim($str) == '地图' || 
                        trim($str) == '在哪' ||
                        trim($str) == '门' ||
                        trim($str) == '怎么走' ||
                        trim($str) == '怎么去' ||
                        trim($str) == '多远' ||
                        trim($str) == '距离' ||
                        trim($str) == '回复') {
                    $content = "你可以在对话框内回复景区名称，进入景区页面可以查看景区平面地图。<a href='http://v2.jingqubao.com/app/app.html'>"
                            . "你也可以戳我，下载APP</a>，景区的地图导航马上呈现，再也不用担心在景区迷路了。\n\n更多问题就去骚扰客服提谱菌吧";
                        } else {
                    $content=$this->weixinTextResponse($str);
                        }
                $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$this->redis->get('qr_access_token').'&openid='.$openId.'&lang=zh_CN';
                if(!$content){
                    $userInfo = json_decode(Http::http_request_json($url),true);
                    $content = ($userInfo['nickname']?$userInfo['nickname'].',':'')."抱歉啊，亲爱的小伙伴，你回复的内容我们暂时没有收录，你可以选择下方菜单栏中的“体验景区宝”了解附近景区的信息。";
                }
            }
            
            if(is_array($content)){
                if (isset($content[0]['PicUrl'])){
                    $result = $this->transmitNews($object, $content);
                }else if (isset($content['MusicUrl'])){
                    $result = $this->transmitMusic($object, $content);
                }
            }else{
                $result = $this->transmitText($object, $content);
            }
        }
		
        return $result;
    }

    //接收图片消息
    private function receiveImage($object)
    {
        $content = array("MediaId"=>$object->MediaId);
        $result = $this->transmitImage($object, $content);
        return $result;
    }

    //接收位置消息
    private function receiveLocation($object)
    {
        $content = "你发送的是位置，纬度为：".$object->Location_X."；经度为：".$object->Location_Y."；缩放级别为：".$object->Scale."；位置为：".$object->Label;
        $result = $this->transmitText($object, $content);
        return $result;
    }

    //接收语音消息
    private function receiveVoice($object)
    {
        if (isset($object->Recognition) && !empty($object->Recognition)){
            $content = "你刚才说的是：".$object->Recognition;
            $result = $this->transmitText($object, $content);
        }else{
            $content = array("MediaId"=>$object->MediaId);
            $result = $this->transmitVoice($object, $content);
        }

        return $result;
    }

    //接收视频消息
    private function receiveVideo($object)
    {
        $content = array("MediaId"=>$object->MediaId, "ThumbMediaId"=>$object->ThumbMediaId, "Title"=>"", "Description"=>"");
        $result = $this->transmitVideo($object, $content);
        return $result;
    }

    //接收链接消息
    private function receiveLink($object)
    {
        $content = "你发送的是链接，标题为：".$object->Title."；内容为：".$object->Description."；链接地址为：".$object->Url;
        $result = $this->transmitText($object, $content);
        return $result;
    }

    //回复文本消息
    private function transmitText($object, $content)
    {
        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[%s]]></Content>
</xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }

    //回复图片消息
    private function transmitImage($object, $imageArray)
    {
        $itemTpl = "<Image>
    <MediaId><![CDATA[%s]]></MediaId>
</Image>";

        $item_str = sprintf($itemTpl, $imageArray['MediaId']);

        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[image]]></MsgType>
$item_str
</xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //回复语音消息
    private function transmitVoice($object, $voiceArray)
    {
        $itemTpl = "<Voice>
    <MediaId><![CDATA[%s]]></MediaId>
</Voice>";

        $item_str = sprintf($itemTpl, $voiceArray['MediaId']);

        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[voice]]></MsgType>
$item_str
</xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //回复视频消息
    private function transmitVideo($object, $videoArray)
    {
        $itemTpl = "<Video>
    <MediaId><![CDATA[%s]]></MediaId>
    <ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
    <Title><![CDATA[%s]]></Title>
    <Description><![CDATA[%s]]></Description>
</Video>";

        $item_str = sprintf($itemTpl, $videoArray['MediaId'], $videoArray['ThumbMediaId'], $videoArray['Title'], $videoArray['Description']);

        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[video]]></MsgType>
$item_str
</xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //回复图文消息
    private function transmitNews($object, $newsArray)
    {
        if(!is_array($newsArray)){
            return;
        }
        $itemTpl = "    <item>
        <Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
        <PicUrl><![CDATA[%s]]></PicUrl>
        <Url><![CDATA[%s]]></Url>
    </item>
";
        $item_str = "";
        foreach ($newsArray as $item){
            $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
        }
        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[news]]></MsgType>
<ArticleCount>%s</ArticleCount>
<Articles>
$item_str</Articles>
</xml>";
		
        //$result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), count($newsArray));
		$time = (string)time();
		$count = (string)count($newsArray);
		$result = "<xml>
<ToUserName><![CDATA[$object->FromUserName]]></ToUserName>
<FromUserName><![CDATA[$object->ToUserName]]></FromUserName>
<CreateTime>$time</CreateTime>
<MsgType><![CDATA[news]]></MsgType>
<ArticleCount>$count</ArticleCount>
<Articles>
$item_str</Articles>
</xml>";
		return $result;
    }

    //回复音乐消息
    private function transmitMusic($object, $musicArray)
    {
        $itemTpl = "<Music>
    <Title><![CDATA[%s]]></Title>
    <Description><![CDATA[%s]]></Description>
    <MusicUrl><![CDATA[%s]]></MusicUrl>
    <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
</Music>";

        $item_str = sprintf($itemTpl, $musicArray['Title'], $musicArray['Description'], $musicArray['MusicUrl'], $musicArray['HQMusicUrl']);

        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[music]]></MsgType>
$item_str
</xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //回复多客服消息
    private function transmitService($object)
    {
        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[transfer_customer_service]]></MsgType>
</xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //日志记录
    private function logger($log_content)
    {
		$max_size = 10000;
		$log_filename = "log.xml";
		if(file_exists($log_filename) and (abs(filesize($log_filename)) > $max_size)){unlink($log_filename);}
		file_put_contents($log_filename, date('H:i:s')." ".$log_content."\r\n", FILE_APPEND);   
    }
    
    
    /*微信文本消息请求处理*/
    private function weixinTextResponse($text){
        $brief = '';
        $photo = '';
        $rid = '';
        $id = '';
        $title = '';
        file_put_contents('/var/hpduan/testyy.txt', "weixin response \n", FILE_APPEND);
        //$semantics = $this->parsingSemantic($text);
        if(count($semantics) == 0 || $semantics == null) {
            $semantics[] = $text;
        }
        file_put_contents('/var/hpduan/testyy.txt', var_export($semantics, TRUE), FILE_APPEND);
        if(!$semantics) {
            file_put_contents('/var/hpduan/unrecognized.log', $text."\n", FILE_APPEND);
        }
//        $semantics = $text;
        $num = 0;
        
        
        model('Tongji')->add_tj("", "", "", "", "weixin_message", "", "w3g", $text, 'weixin_message');
        if($semantics == null){
            return false;
        }

        if($info=$this->is_scenic($semantics)){
            if(count($info) > 1) {
                foreach($info as $scenic) {
                    if($num++==0) {
                        continue;
                    }
                    $brief .= $scenic['scenic_region_name'];
                    $brief .= '   ';
                }
                $brief = '如果这不是您要找的景区，我们还匹配到 （'.$brief.'） 请在输入框中详细输入想要查找的景区名。';
            } else {
                $brief = $info[0]['brief'];
            }
            $title = $info[0]['scenic_region_name'];
            $photo=$info[0]['photo'];
            $rid = $info[0]['scenic_region_id'];
            $id = $info[0]['scenic_region_id'];
            
            $targetUrl = urlencode(U('w3g/Scenic/index',array('rid'=>$id,'type'=>1)));
            $baseUrl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxdcb447934ddf8be6&redirect_uri=" . $targetUrl . "&response_type=code&scope=snsapi_base&state=1#wechat_redirect";

            $content[] = array("Title"=>$title , "Description"=>$brief,"PicUrl"=>$photo,"Url"=>$baseUrl);
            return $content;
        }elseif($info=$this->is_spots($semantics)){
            
            if(count($info) > 1) {
                foreach($info as $spot) {
                    if($num++==0) {
                        continue;
                    }
                    $brief .= $spot['scenic_spot_name'];
                    $brief .= '   ';
                }
                $brief = '如果这不是您要找的景点，我们还匹配到 （'.$brief.'） 请在输入框中详细输入想要查找的景点名。';
            } else {
                $brief = $info[0]['brief'];
            }
            $title = $info[0]['scenic_spot_name'];
            $photo=$info[0]['photo'];
            $rid = $info[0]['rid'];
            $id = $info[0]['scenic_spot_id'];
            
            
            $targetUrl = urlencode(U('w3g/Scenic/index',array('rid'=>$id,'type'=>2)));
            $baseUrl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxdcb447934ddf8be6&redirect_uri=" . $targetUrl . "&response_type=code&scope=snsapi_base&state=1#wechat_redirect";

            $content[] = array("Title"=>$title , "Description"=>$brief,"PicUrl"=>$photo,"Url"=>$baseUrl);
            return $content;
        }else{
            return false;
        }
    }
    /*判断是否是景区*/
    private function is_scenic($names){
        if($names == null) {
            return false;
        }
        foreach($names as $name) {
            if($name == null) {
                continue;
            }
            $regex = new MongoRegex("/".$name."/i");
            $result = MDScenicModel::find(array('scenic_region_name'=>$regex), 
                    array('fields'=>array('scenic_region_name','photo','brief','scenic_region_id'),'return_type'=>1));//model('Scenic')->searchByWeixinKey($name);
            if($result != null) {
                $result = $result['documents'];
                usort($result, function($a, $b){
                    if(strlen($a['scenic_region_name']) > strlen($b['scenic_region_name'])) {
                       return 1; 
                    } else if(strlen($a['scenic_region_name']) < strlen($b['scenic_region_name'])) {
                        return -1;
                    } else {
                        return 0;
                    }
                    
                });
                break;
            }
        }
//        $result = model('Scenic')->searchByWeixinKey($names);
        
        if($result){
            return $result;
        }else{
            return false;
        }
    }
    /*判断是否是景点*/
    private function is_spots($names){
        if($names == null) {
            return false;
        }
        foreach($names as $name) {
            $regex = new MongoRegex("/".$name."/i");
            $result = MDSpotsModel::find(array('scenic_spot_name'=>$regex), 
                    array('fields'=>array('scenic_spot_id','scenic_spot_name','rid','brief','photo'), 'return_type'=>1));//model('Spot')->searchByWeixinKey($name);
            if($result != null) {
                $result = $result['documents'];
                break;
            }
        }
//        $result = model('Spot')->searchByWeixinKey($names);
                    
        if($result){
            return $result;
        }else{
            return false;
        }
    }
    
    public function modifyMenu($menuJson) {
        $redis = new redis();
        
        $redisConfig = C('REDIS_ADDRESS');
        $redis->connect($redisConfig[0]['ip'], $redisConfig[0]['port']);
        $access_token = $redis->get('qr_access_token');
        if (!$access_token) {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . appid . "&secret=" . appsecret;
            $json = Http::http_request_json($url);
            $data = json_decode($json, true);
            $redis->setex('qr_access_token', 7100, $data['access_token']);
            $access_token = $data['access_token'];
        }
        
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=" . $access_token;

        $res = Http::http_request_json($url, json_encode($data));
        $redis->close();
        file_put_contents('/var/hpduan/weixin.log', var_export($res, TRUE), FILE_APPEND);
        return json_decode($res, TRUE);
    }
    
    

}
