@extends('Shared.Layouts.Master')

@section('title')
    @parent
    活动调查
@stop

@section('top_nav')
@include('ManageEvent.Partials.TopNav')
@stop

@section('menu')
@include('ManageEvent.Partials.Sidebar')
@stop

@section('page_title')
<i class='ico-code mr5'></i>
活动调查
@stop

@section('head')

@stop

@section('page_header')
<style>
    .page-header {display: none;}
</style>
@stop


@section('content')
<div class="row">


    <div class="col-md-12">

        <div class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <h4>HTML内嵌代码</h4>
                            <textarea rows="7" onfocus="this.select();"
                                      class="form-control">{{$event->embed_html_code}}</textarea>
                    </div>
                    <div class="col-md-6">
                        <h4>介绍</h4>

                        <p>
                            简单复制和粘贴输入的HTML到网站的任何位置，像组件一样显示。
                        </p>

                        <h5>
                            <b>嵌入预览</b>
                        </h5>

                        <div class="preview_embed" style="border:1px solid #ddd; padding: 5px;">
                            {!! $event->embed_html_code !!}
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@stop
