<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


require_once 'WeixinBase.class.php';

/*
 * 摇一摇代码，根据项目完善
 */
class WeixinShake extends WeixinBase {

    private $equipApplyUrl = "https://api.weixin.qq.com/shakearound/device/applyid?access_token=@token";
    private $equipUpdateUrl = "https://api.weixin.qq.com/shakearound/device/@method?access_token=@token";

    /*
     * 当申请个数小于等于500时，
        {
        "data": {
               "apply_id": 123,
               "device_identifiers":[
                                {
                                        "device_id":10100,	
                                        "uuid":"FDA50693-A4E2-4FB1-AFCF-C6EB07647825",		
                                        "major":10001,
                                        "minor":10002
                                }
                        ]
            },
            "errcode": 0,
            "errmsg": "success."
        }
        当申请个数大于500时，
        {
        "data": {
                       "apply_id": 123,
                        "audit_status": 0,	
                        "audit_comment": "审核未通过"	
           },
           "errcode": 0,
           "errmsg": "success."
        }
     */
    public function applyEquip($quantity, $applyReason, $comment, $poiId) {
        $params['quantity'] = $quantity;
        $params['apply_reason'] = $applyReason;
        $params['comment'] = $comment;
        $params['poi_id'] = $poiId;
        $json = Http::http_request_json($this->equipApplyUrl, $params);
        $data = json_decode($json, true);
    }
    
    public function updateEquipInfo($deviceId, $uuid, $major, $minor, $comment, $location) {
        if($deviceId != null) {
            $params['device_id'] = $deviceId;
        } else {
            $params['uuid'] = $uuid;
            $params['major'] = $major;
            $params['minor'] = $minor;
        }
        if($comment != null) {
            $this->updateEquipComment($params, $comment);
        } else if($location != null) {
            $this->updateEquipLocation($params, $location);
        }
    }
    
    public function updateEquipComment($params, $comment) {
        $params['device_identifier'] = $params;
        $params['comment'] = $comment;
        
        $url = str_replace('@method', 'update', $this->equipUpdateUrl);
        $json = Http::http_request_json($url, $params);
        $data = json_decode($json, true);
    }
    
    public function updateEquipLocation($params, $location) {
        $params['device_identifier'] = $params;
        $params['poi_id'] = $location;
        
        $url = str_replace('@method', 'bindlocation', $this->equipUpdateUrl);
        $json = Http::http_request_json($url, $params);
        $data = json_decode($json, true);
    }
    
    public function getEquipList() {
        //http://mp.weixin.qq.com/wiki/15/b9e012f917e3484b7ed02771156411f3.html
    }
}