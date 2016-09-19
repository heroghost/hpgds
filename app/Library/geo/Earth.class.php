<?php

define('EARTH_RADIUS', 6378.137);//地球半径 
define('PI', 3.1415926); 
class Earth {
    /** 
    * 计算两组经纬度坐标 之间的距离 
    * params ：lat1 纬度1； lng1 经度1； lat2 纬度2； lng2 经度2； len_type （1:m or 2:km); 
    * return m or km 
    */ 
    public function getDistance($lat1, $lng1, $lat2, $lng2, $len_type = 1, $decimal = 2) 
    { 
        $radLat1 = $lat1 * PI / 180.0;
        $radLat2 = $lat2 * PI / 180.0;
        $a = $radLat1 - $radLat2;
        $b = ($lng1 * PI / 180.0) - ($lng2 * PI / 180.0);
        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2)));
        $s = $s * EARTH_RADIUS;
        $s = round($s * 1000);
        if ($len_type > 1) {
            $s /= 1000;
        }
        return round($s, $decimal); 
    } 
    
    public function getDistanceOfCoordinates($c1, $c2, $len_type=1,$decimal = 2) {
        return $this->getDistance($c1[1], $c1[0], $c2[1], $c2[0], $len_type, $decimal);
    }
    
}

