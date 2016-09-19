<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Weather
 *
 * @author Administrator
 */
class Weather {
    
    private $weatherUrl = 'http://apis.baidu.com/heweather/weather/free?city=';//'http://apistore.baidu.com/microservice/weather?cityname=';
    private $weatherTxt = array(
        '00'=>'晴',
        '01'=>'多云',
        '02'=>'阴',
        '03'=>'阵雨',
        '04'=>'雷阵雨',
        '05'=>'雷阵雨伴有冰雹',
        '06'=>'雨夹雪',
        '07'=>'小雨',
        '08'=>'中雨',
        '09'=>'大雨',
        '10'=>'暴雨',
        '11'=>'大暴雨',
        '12'=>'特大暴雨',
        '13'=>'阵雪',
        '14'=>'小雪',
        '15'=>'中雪',
        '16'=>'大雪',
        '17'=>'暴雪',
        '18'=>'雾',
        '19'=>'冻雨',
        '20'=>'沙尘暴',
        '21'=>'小到中雨',
        '22'=>'中到大雨',
        '23'=>'大到暴雨',
        '24'=>'暴雨到大暴雨',
        '25'=>'大暴雨到特大暴雨',
        '26'=>'小到中雪',
        '27'=>'中到大雪',
        '28'=>'大到暴雪',
        '29'=>'浮尘',
        '30'=>'扬沙',
        '31'=>'强沙尘暴',
        '53'=>'霾',
        '99'=>'无'
    );
    
    
    public function getWeatherByLocation($location) {
        $location = $this->filterLocation($location);
        
        $ch = curl_init();
        $url = $this->weatherUrl.$location;
        $header = array(
            'apikey: ccadd7e6bb68252c8432830842b32078',
        );
        // 添加apikey到header
        curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 执行HTTP请求
        curl_setopt($ch , CURLOPT_URL , $url);
        $res = curl_exec($ch);
        
        file_put_contents('/tmp/weather.log',$location."\n", FILE_APPEND);
        file_put_contents('/tmp/weather.log',$res."\n", FILE_APPEND);
        //$weather = file_get_contents($this->weatherUrl.$location);
        $weatherArr = json_decode($res, TRUE);
        if($weatherArr['HeWeather data service 3.0'][0]['status'] != 'ok') {
            return false;
        }
        $weatherArr = $weatherArr['HeWeather data service 3.0'][0]['now'];
        $weatherCodes = array_flip($this->weatherTxt);
        $formatWeather = array(
            'lng'=>'',
            'lat'=>'',
            'altitude'=>'',
            'sunrise'=>'',
            'sunset'=>'',
            'temp'=>$weatherArr['tmp'],
            'h_tmp'=>'',
            'l_tmp'=>'',
            'weather'=>$weatherArr['cond']['txt'],
            'url'=>'http://7u2psp.com1.z0.glb.clouddn.com/weather/weather'.$weatherCodes[$weatherArr['cond']['txt']].'.png',
        );
        return $formatWeather;
    }
    
    private function filterLocation($location) {
        
        $location = substr($location, -3, 3) == '省' || substr($location, -3, 3) == '市' || 
                substr($location, -3, 3) == '县' ? substr($location, 0, strlen($location) - 3) : $location;
        return $location;
    }
}
