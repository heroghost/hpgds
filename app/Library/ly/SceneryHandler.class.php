<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SceneryHandler
 *
 * @author dhp
 */
require_once "TcApi.class.php";
class LYCity {
    public $id;
    public $name;
    
    public function __construct($resp) {
        $this->id = $resp['@attributes']['id'];
        $this->name = $resp['@value'];
    }
}
class LYTheme {
    public $themeId;
    public $themeName;
    public function __construct($resp) {
        $this->themeId = $resp['themeId'];
        $this->themeName = $resp['themeName'];
    }
}
class LYPolicy {
    public $policyId;
    public $policyName;
    public $remark;
    public $price;
    public $tcPrice;
    public $pMode;//支付方式 0：景区现付 1：在线支付 3：其他支付
    public $gMode;//取票方式
    public $minT;//最小票数
    public $maxT;//最大票数
    public $dpPrize;//最大可用现金券
    public $orderUrl;//预订跳转地址
    public $realName;//是否支持实名制[1：是 0：否]
    public $useCard;//是否使用身份证[1：是 0：否]
    public $ticketId;//门票类型Id
    public $ticketName;//门票类型名称
    public $bDate;
    public $eDate;
    public $openDateType;//3-特殊日,2-按周,1-按月,0-普通
    public $openDateValue;
    public $closeDate;
    public $newCloseDate;
    public $timeInterval;
    public $vertifyTimeLimit;
    public $advanceDay;
    public $timeLimit;
    public $containItems;
    public $isNeedMail;
    public $minAge;
    public $maxAge;
    public $invoiceInfo;
    public $refundRule;
        
    public function __construct($resp) {
        $this->policyId = $resp['policyId'];
        $this->policyName = $resp['policyName'];
        $this->remark = $resp['remark'];
        $this->price = $resp['price'];
        $this->tcPrice = $resp['tcPrice'];
        $this->pMode = $resp['pMode'];
        $this->gMode = $resp['gMode'];
        $this->minT = $resp['minT'];
        $this->maxT = $resp['maxT'];
        $this->dpPrize = $resp['dpPrize'];
        $this->orderUrl = $resp['orderUrl'];
        $this->realName = $resp['realName'];
        $this->useCard = $resp['useCard'];
        $this->ticketId = $resp['ticketId'];
        $this->ticketName = $resp['ticketName'];
        $this->bDate = $resp['bDate'];
        $this->eDate = $resp['eDate'];
        $this->openDateType = $resp['openDateType'];
        $this->openDateValue = $resp['openDateValue'];
        $this->closeDate = $resp['closeDate'];
        $this->newCloseDate = $resp['newCloseDate'];
        $this->timeInterval = $resp['timeInterval'];
        $this->vertifyTimeLimit = $resp['vertifyTimeLimit'];
        $this->ticketLimit = $resp['ticketLimit'];
        $this->verifyType = $resp['verifyType'];
        $this->advanceDay = $resp['advanceDay'];
        $this->timeLimit = $resp['timeLimit'];
        $this->containItems = $resp['containItems'];
        $this->isNeedMail = $resp['isNeedMail'];
        $this->minAge = $resp['minAge'];
        $this->maxAge = $resp['maxAge'];
        $this->invoiceInfo = $resp['invoiceInfo'];
        $this->refundRule = $resp['refundRule'];
    }
}
class LYInfo {
    public $nId;
    public $nName;
    public $nContent;
    public function __construct($resp) {
        $this->nId = $resp['nId'];
        $this->nName = $resp['nName'];
        $this->nContent = $resp['nContent'];
    }
}
class LYNotice {
    public $nType;
    public $nTypeName;
    public $nInfos;
    public function __construct($resp) {
        $this->nType = $resp['nType'];
        $this->nTypeName = $resp['nTypeName'];
        foreach($resp['nInfo']['info'] as $info) {
            $ninfos[] = new LYInfo($info);
        }
        $this->nInfos = $ninfos;
    }
}
class LYAhead {
    public $day;
    public $time;
    public function __construct($resp) {
        $this->day = $resp['day'];
        $this->time = $resp['time'];
    }
}
class LYPriceScenery {
    public $sceneryId;
    public $policies;
    public $notices;
    public $ahead;
    
    public function __construct($resp) {
        $this->sceneryId = $resp['sceneryId'];
        if($resp['policy']['p']['policyId'] == null) {
            foreach($resp['policy']['p'] as $p) {
                $policies[] = new LYPolicy($p);
            }
        } else {
            $policies[] = new LYPolicy($resp['policy']['p']);
        }
        $this->policies = $policies;
        if($resp['notice']['n']['nType'] == null) {
            foreach($resp['notice']['n'] as $n) {
                $notices[] = new LYNotice($n);
            }
        } else {
            $notices[] = new LYNotice($resp['notice']['n']);
        }
        $this->notices = $notices;
        $this->ahead = new LYAhead($resp['ahead']);
    }
}
class LYPrice {
    public $header;
    public $priceSceneries;
    
    public function __construct($resp) {
        $this->header = new LYHeader($resp['header']);
        foreach($resp['body']['sceneryList'] as $k=>$scenery) {
            $priceSceneries[] = new LYPriceScenery($scenery);
        }
        $this->priceSceneries = $priceSceneries;
    }
}
class LYScenery {
    public $header;
    public $sceneryId;
    public $sceneryName;
    public $bookFlag;//是否可预订
    public $grade;//景点级别，如AAAAA
    public $address;
    public $city;
    public $province;
    public $intro;
    public $payMode;//在线支付。。。，实际以价格查询结果为准
    public $amountAdvice;//建议价格
    public $lon;
    public $lat;
    public $ifUseCard;//是否需要证件号
    public $theme;
    public $buyNotice;
    
    public function __construct($resp) {
        $this->header = new LYHeader($resp['header']);
        
        $this->sceneryId = $resp['body']['scenery']['sceneryId'];
        $this->sceneryName = $resp['body']['scenery']['sceneryName'];
        $this->bookFlag = $resp['body']['scenery']['bookFlag'];
        $this->grade = $resp['body']['scenery']['grade'];
        $this->address = $resp['body']['scenery']['address'];
        $this->city = new LYCity($resp['body']['scenery']['city']);
        $this->province = new LYCity($resp['body']['scenery']['province']);
        $this->intro = $resp['body']['scenery']['intro'];
        $this->payMode = $resp['body']['scenery']['payMode'];
        $this->amountAdvice = $resp['body']['scenery']['amountAdvice'];
        $this->lon = $resp['body']['scenery']['lon'];
        $this->lat = $resp['body']['scenery']['lat'];
        $this->ifUseCard = $resp['body']['scenery']['ifUseCard'];
        $this->theme = new LYTheme($resp['body']['scenery']['theme']);
        $this->buyNotice = $resp['body']['scenery']['buyNotice'];
    }
}
class LYPerson {
    public $name;
    public $mobile;
    public $email;
    public $address;
    public $postcode;
    public $idcard;
    public function __construct($name, $mobile, $email="", $address="", $postcode="", $idcard =""){
        $this->name = $name;
        $this->mobile = $mobile;
        $this->email = $email;
        $this->address = $address;
        $this->postcode = $postcode;
        $this->idcard = $idcard;
    }
}
class LYOrder {
    public $serialId;
    public $orderStatus;
    public $createDate;
    public $travelDate;
    public $sceneryId;
    public $sceneryName;
    public $bookingMan;
    public $guests;
    public $ticketName;
    public $ticketQuantity;
    public $ticketPrice;
    public $ticketAmount;
    public $prizeAmount;
    public $payStatus;//0:此门票无需线上支付 1:此门票需要线上支 2:此门票全额支付成功 3:此门票部分支付成功 : 如只需知道是否为在线支付 只判断 状态非0即可
    public $enableCancel;
    public $ticketTypeId;
    public $currentPayStatus;//0：无需支付 1：待支付 2：已支付（全部） 3：已支付（部分） 4：已退款
    public $refundTime;
    
    public function __construct($resp) {
        $this->serialId = $resp['serialId'];
        $this->orderStatus = $resp['orderStatus'];
        $this->createDate = $resp['createDate'];
        $this->travelDate = $resp['travelDate'];
        $this->sceneryId = $resp['sceneryId'];
        $this->sceneryName = $resp['sceneryName'];
        $this->bookingMan = new LYPerson($resp['bookingMan'],$resp['bookingMobile']);
        $this->guests[] = new LYPerson($resp['guestName'], $resp['guestMobile']);
        $this->ticketName = $resp['ticketName'];
        $this->ticketQuantity = $resp['ticketQuantity'];
        $this->ticketPrice = $resp['ticketPrice'];
        $this->ticketAmount = $resp['ticketAmount'];
        $this->prizeAmount = $resp['prizeAmount'];
        $this->payStatus = $resp['payStatus'];
        $this->enableCancel = $resp['enableCancel'];
        $this->ticketTypeId = $resp['ticketTypeId'];
        $this->currentPayStatus = $resp['currentPayStatus'];
        $this->refundTime = $resp['refundTime'];
    }
}
class LYHeader {
    public $actionCode;
    public $rspType;
    public $rspCode;
    public $rspDesc;
    public $digitalSign;
    public $rspTime;
    
    public function __construct($resp) {
        $this->actionCode = $resp['actionCode'];
        $this->rspType = $resp['rspType'];
        $this->rspCode = $resp['rspCode'];
        $this->rspDesc = $resp['rspDesc'];
        $this->digitalSign = $resp['digitalSign'];
        $this->rspTime = $resp['rspTime'];
        
    }
}
class SceneryHandler {
    
    private function _scenryService($serviceName, $paramArr, $returnArray=true) {
        $tcApi = new TcApi();
        return $tcApi->s('http://tcopenapi.17usoft.com/handlers/scenery/queryhandler.ashx', $serviceName, $paramArr, $returnArray);
    }
    
    private function _orderService($serviceName, $paramArr, $returnArray=true) {
        $tcApi = new TcApi();
        return $tcApi->s('http://tcopenapi.17usoft.com/handlers/scenery/orderhandler.ashx', $serviceName, $paramArr, $returnArray);
    }
       
    public function getSceneryDetail($paramArr, $returnArray=true) {
        if(!in_array('sceneryId', array_keys($paramArr))) {
            throw new Exception('参数缺失');
        }
        $resp = $this->_scenryService('GetSceneryDetail', $paramArr, $returnArray);
        return new LYScenery($resp);
    }
    
    public function getSceneryPrice($sceneryId, $returnArray=true) {
        $resp = $this->_scenryService('GetSceneryPrice', array('sceneryIds'=>$sceneryId), $returnArray);
        return new LYPrice($resp);
    }
    
    public function getSceneryList($latitude, $longitude, $radius = 5000, $page=1, $pagesize=20) {
        $params = array('page'=>$page, 'pageSize'=>$pagesize);
        if($latitude != null && $longitude != null) {
            $params['latitude'] = $latitude;
            $params['longitude'] = $longitude; 
            $params['radius'] = $radius;
        }
        $rows = $this->_scenryService('GetSceneryList', $params);
        return $rows;
    }
    
    public function getSceneryTraffic($sceneryId, $returnArray=true) {
        return $this->_scenryService('GetSceneryTrafficInfo', array('sceneryIds'=>$sceneryId), $returnArray);
    }
    
    public function getSceneryImages($sceneryId, $page=1, $pagesize=20, $returnArray=true) {
        $params = array('page'=>$page, 'pageSize'=>$pagesize, 'sceneryId'=>$sceneryId);
        return $this->_scenryService('GetSceneryImageList', $params, $returnArray);
    }
    
    public function getSceneryNearby($sceneryId, $page=1, $pagesize=20, $returnArray=true) {
        $params = array('page'=>$page, 'pageSize'=>$pagesize, 'sceneryId'=>$sceneryId);
        return $this->_scenryService('GetNearbyScenery', $params, $returnArray);
    }
    
    public function getPriceCalendar($policyId, $startDate, $endDate, $isAutoShowPrice = 0, $returnArray=true) {
        $params = array('policyId'=>$policyId, 'startDate'=>$startDate, 'endDate'=>$endDate, 'isAutoShowPrice '=>$isAutoShowPrice);
        return $this->_scenryService('GetPriceCalendar', $params, $returnArray);
    }
    
    public function submitSceneryOrder($sceneryId, $policyId, $travelDate, $bMan, $tMan, $tNum, $gMans, $returnExtra = 0, $returnArray=true) {
        $params = array(
            'sceneryId'=>$sceneryId,
            'bMan'=>$bMan['name'],
            'bMobile'=>$bMan['mobile'],
            'tName'=>$tMan['name'],
            'tMobile'=>$tMan['mobile'],
            'policyId'=>$policyId,
            'tickets'=>$tNum,
            'travelDate'=>$travelDate,
            'orderIP'=>$bMan['ip'],
            'isExtra'=>$returnExtra
        );
        if($bMan['address'] != null) {
            $params['bAddress'] = $bMan['address'];
        }
        if($bMan['postcode'] != null) {
            $params['bPostCode'] = $bMan['postcode'];
        }
        if($bMan['email'] != null) {
            $params['bEmail'] = $bMan['email'];
        }
        if($bMan['idcard'] != null) {
            $params['idCard'] = $bMan['idcard'];
        }
        if($gMans != null) {
            foreach($gMans as $man) {
                $params['otherGuest'][] = array(
                    'guest'=>array(
                        'gName'=>$man['name'],
                        'gMobile'=>$man['mobile'],
                        'idCard'=>$man['idcard'],
                        'email'=>$man['email'],
                    )
                );
            }
        }
        return $this->_orderService('SubmitSceneryOrder', $params, $returnArray);
    }

    public function cancelSceneryOrder($serialId, $cancelReason) {
        if(!in_array($cancelReason, array(1,2,3,4,5,12,17,18))) {
            throw new Exception("取消原因错误");
        }
        $params = array(
            'serialId'=>$serialId,
            'cancelReason'=>$cancelReason    
        );
        return $this->_orderService('CancelSceneryOrder', $params);

    }
    
    public function getSceneryOrderList($cStartDate, 
            $cEndDate, $tStartDate, $tEndDate, $orderStatus,
            $serialId, $bookingMan, $bookingMobile, $guestName,
            $guestMobile,$returnExtra, $page = 1, $pageSize = 20) {
        
        $params = array(
            'page'=>$page,
            'pageSize'=>$pageSize,
            'cStartDate'=>$cStartDate ,
            'cEndDate'=>$cEndDate ,
            'tStartDate'=>$tStartDate ,
            'tEndDate'=>$tEndDate ,
            'orderStatus'=>$orderStatus ,
            'serialId'=>$serialId ,
            'bookingMan'=>$bookingMan ,
            'bookingMobile'=>$bookingMobile ,
            'guestName'=>$guestName ,
            'guestMobile'=>$guestMobile ,
            'isExtra'=>$returnExtra     
        );
        $resp = $this->_orderService('GetSceneryOrderList', $params);
        return $resp;
    }
    
    public function getSceneryOrderDetail($serialIds, $isExtra) {
        $params = array(
            'serialIds'=>$serialIds,
            'isExtra'=>$isExtra
        );
        $resp = $this->_orderService('GetSceneryOrderDetail', $params);
        foreach($resp['body']['orderList'] as $order) {
            $lyorders[] = new LYOrder($order);
        }
        $header = new LYHeader($resp['header']);
        return $lyorders;
    }
    
    public function mobileWapPay($name, $desc, $num, $serialId, $amount) {
        $tcApi = new TcApi();
        $res = $tcApi->tcPay($desc, $name, $num, $serialId, $amount);
        return $res;
        /*
         * {
"payUrl":
"http://wappaygw.alipay.com/service/rest.htm?call_back_url=http://www.17u.cn&format=xml&partner=2088701808645180&req_dat
a=<auth_and_execute_req><request_token>20131029de657e1162b3e9b3dd2159fcd921a5aa</request_token></auth_and_execute_
req>&sec_id=MD5&service=alipay.wap.auth.authAndExecute&v=2.0&sign=b0edbea80a319ed4c57a65f7b09afe79",
"respCode": 1000,
"respMsg": "支付信息提交成功",
"respTime": "2013-10-29 16:36:36.6315"
}
         * 
         * 
         */
    }
}
