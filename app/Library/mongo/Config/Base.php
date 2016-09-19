<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Base
 *
 * @author hpduan
 */



class Base
{

    static function getEnv()
    {
        $env = getenv('APPLICATION_ENV');
        return $env?$env:'development';
    }

   static function getHttpHost() {
        $env = static::getEnv();
        $httpHost = '';
        $urlPrefix = 'http://';
        if($_SERVER['HTTPS'] === 'on'){
            $urlPrefix = 'https://';
        }
        switch ($env) {
            case 'production':
                $httpHost = $urlPrefix.'www.jingqubao.com';
                break;
            case 'testing':
                $httpHost = $urlPrefix.'test.jingqubao.com';
                break;
            default :
                $httpHost = $urlPrefix.'www.jingqubao.com';
                break;
        }
        return $httpHost;
    }
    
    
    public static function isDevelopment(){
        if(static::getEnv() === 'development'){
            return true;
        }
        return false;
    }
    
    public static function isTesting(){
        if(static::getEnv() === 'testing'){
            return true;
        }
        return false;
    }
    
    public static function isProduction(){
        if(static::getEnv() === 'production'){
            return true;
        }
        return false;
    }
}

?>
