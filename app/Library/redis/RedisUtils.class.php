<?php

/**
 +------------------------------------------------------------------------------
 * Redis uitils
 +------------------------------------------------------------------------------
 * @subpackage  Util
 * @author    lb
 * @version   
 +------------------------------------------------------------------------------
 */
class RedisUtils {
	
	static function checkMd5AndDelete($keyName,$checkVal){
		$redis = new redis();
		//check code
		$redisConfig = C('REDIS_ADDRESS');
		$redis->connect($redisConfig[0]['ip'], $redisConfig[0]['port']);
		
		if ($redis->get($keyName) && $redis->get($keyName) == ( md5(strtoupper($checkVal)) )) {
			$redis->delete($keyName);
			return 1;
		} else {
			return -1;
		}
	}
	
	static function putVal($name,$value,$time){
		
		$redis = new redis();
		$redisConfig = C('REDIS_ADDRESS');
		$redis->connect($redisConfig[0]['ip'], $redisConfig[0]['port']);
		
		if (!$redis->get($name)) {
			$redis->setex($name, $time, $value);
		}
		
	}
}