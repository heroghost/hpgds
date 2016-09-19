<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MongodbBase
 *
 * @author hpduan
 */

//namespace Etour\Gds\Base;

class MongoDocument {

    protected $db = null;
    protected $collection = null;
    protected $docAr = array();
    protected $langCode = null;
    private $error =  '';

    public function __construct($docAr, $db = '', $collection = '', $langCode = null) {
        $this->langCode = $langCode;
        $this->docAr = $docAr;
        $this->db = $db;
        $this->collection = $collection;
        if($langCode && $docAr['LangCode'] !== $langCode){
            static::translate($this->docAr, $langCode);
        }
    }

    public function __call($name, $arguments) {
        if (false !== strpos($name, 'get')) {
            $tmp = explode('get', $name);
            return $this->docAr[$tmp[1]];
        } else if (false !== strpos($name, 'set')) {
            $tmp = explode('set', $name);
            return $this->docAr[$tmp[1]] = $arguments[0];
        }
    }

    public function toArray() {
        return $this->docAr;
    }
    public static function escapeCharaters($str) {
        //顺序不能轻易改动
        $specialCharaters = array('\\',
                                  '\'',
                                  '"',
                                  '(',
                                  ')',
                                  ';',
                                  '&',
                                  '.',
                                  ',',
                                  '$',
                                  '-',
                                  '_',
                                  '/',
                                  );
        foreach($specialCharaters as $specialCharater) {
            if($specialCharater == '&') {
                $str = str_replace($specialCharater, '&amp;', $str);
                continue;
            }
            $str = str_replace($specialCharater, '\\'.$specialCharater, $str);
        }
        return $str;
    } 
    public static function translate(&$docArr, $langCode){
        if(!$langCode){
            return $docArr;
        }
        
        if(!is_array($docArr)) return $docArr;
        if($docArr['LangCode'] && $docArr['LangCode']!=$langCode && $docArr['LangVs']){
            foreach($docArr['LangVs'] as $langArr){
                if($langArr['LangCode'] == $langCode){
                    $docArr['LangCode'] = $langCode;
                    $docArr['TextInfo'] = $langArr['TextInfo'];
                    break;
                }
            }
        }
        
        elseif($docArr['LangVs']) {
            foreach($docArr['LangVs'] as $langArr){
                if($langArr['LangCode'] == $langCode){
                    $docArr['TextInfo'] = $langArr['TextInfo'];
                    break;
                }
            }
        }
        
        else{}
        foreach($docArr as $k=>&$v){
            if(!is_array($v)){
                continue;
            }
            $docArr[$k] = static::translate($v, $langCode);
        }
        return $docArr;
    }

    public function __set($name, $value) {
        return $this->docAr[$name] = $value;
    }

    public function __get($name) {
        return $this->docAr[$name];
    }

    public function preInsert() {
        if (!$this->isVaild()) {
            throw new \Exception('Document invalid ');
        }

        $this->format();
    }

    protected function format() {
        
    }

    protected function isVaild() {
        return true;
    }
    public function getError(){
        return $this->error;
    }
    public function setError($error){
        $this->error = $error;
    }

}

?>
