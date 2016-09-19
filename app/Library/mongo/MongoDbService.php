<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MongoService
 *
 * @author hpduan
 */

//namespace Etour\Gds\Base;
//
//use Etour\Gds\Util\Math;
//use Etour\Gds\Base\MongoDbClientManager;
//use Etour\Gds\Base\MongoDocument;

require_once  __DIR__.'/Util/Math.php';
require_once  __DIR__.'/MongoDbClientManager.php';
require_once  __DIR__.'/MongoDocument.php';
class MongoDbService {

    static $self = null;

    private function __construct() {
        
    }

    private function __clone() {
        
    }
    public static function getDocumentById($id, $options = array()){
        if(!$id){
            return null;
        }
        if(!($id instanceof \MongoId)){
            $id = new \MongoId($id);
        }
        $ret = static::find(array(
            '_id'=>$id
        ), $options);
        if(!$ret['total']){
            return null;
        }
            
        return $ret['documents'][0];
    }

    public static function getDocumentByCode($code, $fields = array(), $return_type = 0, $langCode = null, $status = array())
    {
        $where = array('Code' => $code);
        if($status){
            $where['Status'] = array('$in' => $status);
        }
        $ret = static::find( $where
                , array(
                    'fields' => $fields,
                    'langCode' => $langCode,
                    'return_type' => $return_type
                ));
        return $ret['documents'][0];
    }

    public static function getDocumentByCodes($codes, $fields = array(), $langCode=null, $status = null, $return_type=1) {
        if(!$codes){
            return array();
        }
        $codes = array_unique($codes);
        if ($fields && !$fields['Code']) {
            $fields['Code'] = 1;
        }
        if($status) $result = static::find(array('Code' => array('$in' => $codes), 'Status'=>array('$in'=>$status)), array('fields'=>$fields, 'return_type'=>$return_type, 'langCode'=>$langCode));
        else   $result = static::find(array('Code' => array('$in' => $codes)), array('fields'=>$fields, 'return_type'=>$return_type, 'langCode'=>$langCode));

        $ret = array();
        foreach ($result['documents'] as $doc) {
            if($return_type === 0){
                $ret[$doc->Code] = $doc;
            }
            else{
                $ret[$doc['Code']] = $doc;
            }            
        }
        return $ret;
    }
    
    //$return_type: 返回类型：CodeOrId, Id, Code
    public static function addDocument(MongoDocument $document, $return_type='CodeOrId') {
        if (!$document) {
            return false;
        }

        $document->preInsert();

        $docAr = $document->toArray();

//        if (!$docAr['Code'] && method_exists($document, 'generateCode')) {
//            $docAr['Code'] = $document->generateCode();
//        }

        $ret = static::insert($docAr, array(
                    'w' => 1
                ));

        if ($ret['ok']) {
            if($return_type === 'CodeOrId' || !$return_type) {
                if($docAr['Code']){
                    return $docAr['Code'];
                }
                return (string)$docAr['_id'];
            }elseif($return_type === 'Code') {
                return $docAr['Code'];
            }elseif($return_type === 'Id') {
                return $docAr['_id'];
            }else {
                if($docAr['Code']){
                    return $docAr['Code'];
                }
                return (string)$docAr['_id'];
            }
            
        } else {
            return false;
        }
    }
    
    public static function tranRecursion($code, $langCode, $tranInfo, $fatherIndex) {
        $isLangCode = static::find(array(
                    'Code' => $code
                    , 'LangCode' => $langCode
                ));
        if ($isLangCode['total']) {
            return;
        }
        if(is_array($tranInfo) && count($tranInfo)) {
            if(isset($tranInfo['TextInfo'])) {
                $ret = static::find(array(
                            'Code' => $code
                            , $fatherIndex .'LangVs.LangCode' => $langCode
                        ) , array('fields'=>array(
                            $fatherIndex . 'TextInfo'=>1,
                            $fatherIndex . 'LangVs'=>1
                        )));
                
                $document = $ret['documents'][0];
                
                if ($ret['total']) {
                    $ret = static::update(array(
                        'Code' => $code
                        , $fatherIndex . 'LangVs.LangCode' => $langCode
                            ), array(
                        '$set' => array(
                            $fatherIndex . 'LangVs.$.TextInfo' => $tranInfo['TextInfo']
                        )
                    ));
                } else {
                    static::update(array(
                        'Code' => $code
                            ), array(
                        '$push' => array(
                            $fatherIndex . 'LangVs' => array(
                                'LangCode' => $langCode,
                                'TextInfo' => $tranInfo['TextInfo']
                            )
                        )
                    ));
                }
                
                if(is_null($document)){
                    $rets = static::find(array(
                                'Code' => $code
                            ) , array('fields'=>array(
                                'TextInfo'=>1,
                            )));
                    $document = $rets['documents'][0];
                }
                if($document instanceof \Etour\Gds\Dao\ActivityDocument){
                    $searchString = \Etour\Gds\Service\ActivityService::getSearchString($document);
                }
                
                static::update(array(
                    'Code'=>$code
                ), array(
                    '$set'=>array(
                        'SearchString'=>$searchString
                    )
                ));
            }
            foreach($tranInfo as $k=>$v) {
                if($k === 'LangVs' || $k === 'TextInfo') {
                    continue;
                }
                if($fatherIndex) {
                    static::tranRecursion($code, $langCode, $v, $fatherIndex . $k . '.');
                }else {
                    static::tranRecursion($code, $langCode, $v, $k . '.');
                }
            }
        }
    }

    public static function tran($code, $key, $langCode, $textInfo) {
        switch ($key) {
            case 'TextInfo':
                $isLangCode = static::find(array(
                            'Code' => $code
                            , 'LangCode' => $langCode
                        ));
                if ($isLangCode['total']) {
                    break;
                }
                $ret = static::find(array(
                            'Code' => $code
                            , 'LangVs.LangCode' => $langCode
                        ) , array('fields'=>array(
                            'TextInfo'=>1,
                            'LangVs'=>1
                        )));
                
                $document = $ret['documents'][0];
                
                if ($ret['total']) {
                    $ret = static::update(array(
                        'Code' => $code
                        , 'LangVs.LangCode' => $langCode
                            ), array(
                        '$set' => array(
                            'LangVs.$.TextInfo' => $textInfo
                        )
                    ));
                } else {
                    static::update(array(
                        'Code' => $code
                            ), array(
                        '$push' => array(
                            'LangVs' => array(
                                'LangCode' => $langCode,
                                'TextInfo' => $textInfo
                            )
                        )
                    ));
                }
                
                if(is_null($document)){
                    $rets = static::find(array(
                                'Code' => $code
                            ) , array('fields'=>array(
                                'TextInfo'=>1,
                            )));
                    $document = $rets['documents'][0];
                }
                
                if($document instanceof \Etour\Gds\Dao\ActivityDocument){
                    $searchString = \Etour\Gds\Service\ActivityService::getSearchString($document);
                }
                
                static::update(array(
                    'Code'=>$code
                ), array(
                    '$set'=>array(
                        'SearchString'=>$searchString
                    )
                ));
                
                if($document instanceof \Etour\Gds\Dao\WebSettingDocument){
                    $isPublished = 0;
                    static::update(array(
                        'Code' => $code,
                        'Type' => 0,
                    ), array(
                        '$set'=>array(
                            'IsPublished' => $isPublished
                        )
                    ));
                }
                
                break;
        }
    }

    //options: fields page pn sort return_type
    //return_type: 0: 返回Object，1: 返回数组，默认0
    public static function find($query = array(), $options = array()) {
        
        if (!isset($options['fields'])) {
            $options['fields'] = array();
        }
        else{
            if(!$options['fields']['LangCode'] || !$options['fields']['LangVs']){
                foreach($options['fields'] as $fk => $fv){
                    if(substr($fk, 0, 8) == 'TextInfo'){
                        $options['fields']['LangCode'] = 1;
                        $options['fields']['LangVs'] = 1;
                        break;
                    }
                }
            }            
        }
        if (is_string($query) || ($query instanceof MongoId)) {
            $id = new \MongoId($query);
            $query = array('_id' => $id);
            return static::one($query, $options['fields']);
        }
        
        $documents = array();
        $cursor = static::getMongoCollection()->find($query, array_merge($options['fields']));
        
        if ($options['page']) {
            $options['page'] = intval($options['page']);
            $options['pn'] = intval($options['pn']);
            $options['pn'] = $options['pn'] ? $options['pn'] : 10;
            if ($options['page'] < 1) {
                $options['page'] = 1;
            }
            if ($options['pn'] < 0) {
                $options['pn'] = 10;
            }

            $skip = ($options['page'] - 1) * $options['pn'];
            $cursor = $cursor->skip($skip)->limit($options['pn']);
        }

        if (isset($options['sort']) && is_array($options['sort'])) {
            $cursor = $cursor->sort($options['sort']);
        }
        $total = $cursor->count();
        foreach ($cursor as $docAr) {
            if ($options['return_type'] && $options['return_type'] == 1) {
                if($docAr['LangCode'] !== $options['langCode']) {
                    $documents[] = MongoDocument::translate($docAr, $options['langCode']);
                }
                else $documents[] = $docAr;
            } else {
                $documents[] = static::toDocument($docAr, $options['langCode']);
            }
        }

        $ret = array(
            'total' => $total,
            'documents' => $documents
        );

        if ($options['pn'] && $options['page'] && $options['page'] > 0 && $options['pn'] > 0) {
            $ret['page'] = intval($options['page']);
            $ret['pn'] = intval($options['pn']);
            $ret['totalPage'] = Math::getTotalPage($total, $ret['pn']);
        }

        return $ret;
    }

    public static function insert(array $documentAr, array $options = array()) {
        return static::getMongoCollection(true)->insert($documentAr, $options);
    }

    public static function remove(array $criteria, array $options = array()) {
        // if you want to remove a document by MongoId
        if (array_key_exists('_id', $criteria) && !($criteria["_id"] instanceof \MongoId) && !is_array($criteria['_id'])) {
            $criteria["_id"] = new \MongoId($criteria["_id"]);
        }
        
        return static::getMongoCollection(true)->remove($criteria, $options);
    }

    public static function update(array $criteria, array $object, array $options = array()) {
        $col = static::getMongoCollection(true);
        return @$col->update($criteria, $object, $options);
    }

    public static function drop() {
        return static::getMongoCollection(true)->drop();
    }

    protected static function one(array $query = array(), array $fields = array()) {

        $docAr = static::getMongoCollection()->findOne($query, $fields);
        if (is_null($docAr))
            return null;

        return static::toDocument($docAr);
    }

    public static function getMongoCollection() {
        return static::getMongoDb()->selectCollection(static::$collection);
    }

    protected static function getMongoDb() {
        return MongoDbClientManager::getInstance()
                        ->selectDb(static::$db);
    }
    
    public static function getMongodbErrors(){
        return static::getMongoDb()->lastError();
    }

    private static function toDocument(&$docAr, $langCode = 'en-US') {
        $documentClassName = static::getDocumentClassName();
        tsload(ADDON_PATH.'/model/Document/'.$documentClassName.'.php');
        return new $documentClassName($docAr, $langCode);
    }

    private static function getDocumentClassName() {
        $ar = array();
        $tmp = explode('\\', get_called_class());
        
//        $ar[] = '';
//        $ar[] = 'Etour';
//        $ar[] = 'Gds';
//        $ar[] = 'Dao';
//        $length = count($tmp);
//        if($length > 4){
//            $ar[] = $tmp[$length - 2];
//        }
        
        $tmp2 = explode('_', static::$collection);
        foreach ($tmp2 as $key) {
            $tclassName .= ucwords($key);
        }
        
        
         $tclassName = $tclassName.'Document';
//        var_dump($ar);
        if($length > 4){
            $tclassName = str_replace( $tmp[$length - 2], '', $tclassName);
        }
        
        $ar[] = $tclassName;
        
        $className = implode('\\', $ar);
        
//        $className = strtr($className, '\\\\', '\\');
//      echo $className;exit();
        return $className;
    }

}

?>
