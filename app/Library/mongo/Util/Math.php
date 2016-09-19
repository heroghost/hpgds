<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Math
 *
 * @author hpduan
 */
//namespace Etour\Gds\Util;

class Math
{

    public static function getTotalPage($total, $pn = 10)
    {
        if(!$pn || $pn <= 0){
            throw new \Exception("pagenumber can't be zero");
        }
        if (0 !== $total % $pn) {
            $count = intval($total / $pn) + 1; 
        } else {
            $count = intval($total / $pn);
        }
        
        return $count;
    }

}

