<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Dijkstra
 *
 * @author Administrator
 */
class node {
    public $matrix;
    public $n;
    public $e;
}
class Dijkstra {
    
    public function dijkstraPath($W1, $start, $end) {
        $isLabel = array();// 是否标号
        $indexs = array();// 所有标号的点的下标集合，以标号的先后顺序进行存储，实际上是一个以数组表示的栈  
        $i_count = -1;//栈的顶点
        $distance = $W1[$start];// v0到各点的最短距离的初始值
        $index = $start;// 从初始点开始 
        $presentShortest = 0;//当前临时最短距离
        
        $indexs[++$i_count] = $index;// 把已经标号的下标存入下标集中
        $isLabel[$index] = true;
        
        while($i_count<count($W1[0])) {
            // 第一步：标号v0,即w[0][0]找到距离v0最近的点  
 
            $min = PHP_INT_MAX ;  
            for ($i = 0; i < count($distance); $i++) {  
                if (!$isLabel[$i] && $distance[$i] != -1 && $i != $index) {  
                    // 如果到这个点有边,并且没有被标号    
                    if ($distance[$i] < $min) {
                        $min = $distance[$i];  
                        $index = $i;// 把下标改为当前下标  
                    }  
                }  
            }  
            if ($index == $end) {//已经找到当前点了，就结束程序  
                break;  
            }  
            $isLabel[$index] = true;//对点进行标号  
            $indexs[++$i_count] = $index;// 把已经标号的下标存入下标集中  
            if ($W1[$indexs[$i_count - 1]][$index] == -1 
                    || $presentShortest + $W1[$indexs[$i_count - 1]][$index] > $distance[$index]) {  
                // 如果两个点没有直接相连，或者两个点的路径大于最短路径  
                $presentShortest = $distance[$index];  
            } else {  
                $presentShortest += $W1[$indexs[$i_count - 1]][$index];  
            }  
 
            // 第二步：将distance中的距离加入vi  
            for ($i = 0; i < count($distance); $i++) {  
                // 如果vi到那个点有边，则v0到后面点的距离加  
                if ($distance[$i] == -1 && $W1[$index][$i] != -1) {// 如果以前不可达，则现在可达了  
                    $distance[$i] = $presentShortest + $W1[$index][$i];  
                } else if ($W1[$index][$i] != -1 
                        && $presentShortest + $W1[$index][$i] < $distance[$i]) {  
                    // 如果以前可达，但现在的路径比以前更短，则更换成更短的路径  
                    $distance[$i] = $presentShortest + $W1[$index][$i];  
                }  
 
            }  
        }
        //如果全部点都遍历完，则distance中存储的是开始点到各个点的最短路径  
        return $distance;  
    }
    
    public function getPath($path, $v, $v0) {
        $u = $v;
        $s = array();
        while($v != $v0) {
            $s[] = $v;
            $v = $path[$v];
        }
        $s[] = $v;
        return $s;
    }
}
