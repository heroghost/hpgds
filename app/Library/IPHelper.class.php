<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IPHelper
 *
 * @author dhp
 * Problem:int is too short for ip
 */
class IPHelper {
    public function getIntIp($ipStr) {
        $ipArr = explode('.', $ipStr);
        if(!is_array($ipArr) || count($ipArr) != 4) {
            return false;
        }
        $intIp = ($ipArr[0]<<24)+($ipArr[1]<<16)+($ipArr[2]<<8)+($ipArr[3]);
        
        return $intIp;
    }
}
