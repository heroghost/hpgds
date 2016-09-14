@extends('Shared.Layouts.BlankSlate')


@section('blankslate-icon-class')
    ico-search
@stop

@section('blankslate-title')
    没有查询结果
@stop

@section('blankslate-text')
    没找到跟关键字匹配的内容 '{{isset($search['q']) ? $search['q'] : $q}}'
@stop

@section('blankslate-body')
    
@stop