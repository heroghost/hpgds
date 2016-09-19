<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApiAuthUtil
 *
 * @author dhp
 */
class ApiAuthUtil {
    
    private $module;
    private $category;
    private $methodName;
    private $accountId;
    private $password;
    public $allianceId;
    
    public $requestTime;
    public $digitalSign;
    public function __construct($module, $category, $methodName, $accountId, 
            $password, $requestTime, $allianceId) {
        $this->requestTime = $requestTime;
        $this->module = $module;
        $this->category = $category;
        $this->methodName = $methodName;
        $this->accountId = $accountId;
        $this->password = $password;
        $this->allianceId = $allianceId;
        
        $this->encryptDigitallSign();
    }
    
    public function getApiURL($apiUrl) {
        return "{$apiUrl}?allianceId={$this->allianceId}&digitalSign={$this->digitalSign}&ReqTime={$this->requestTime}";
    }
    
    public function getAuthQueryStringParams() {
        return "allianceId={$this->allianceId}&digitalSign={$this->digitalSign}&ReqTime={$this->requestTime}";
    }
    
    private function encryptDigitallSign() {        
        $fullActionName = strtolower("{$this->module}.{$this->category}.{$this->methodName}");
        $token = strtolower("{$fullActionName}&{$this->accountId}&{$this->password}&{$this->requestTime}");
        
        $digitalSign = sha1($token);//经验证，跟C#源码的加密方式完全一致
        $this->digitalSign = $digitalSign;
    }
}
