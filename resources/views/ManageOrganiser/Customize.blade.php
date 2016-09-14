@extends('Shared.Layouts.Master')

@section('title')
    @parent
    Organiser Events
@stop

@section('page_title')
    {{$organiser->name}} 活动
@stop

@section('top_nav')
    @include('ManageOrganiser.Partials.TopNav')
@stop

@section('head')
    <style>
        .page-header {
            display: none;
        }
    </style>
    <script>
        $(function () {
            $('.colorpicker').minicolors({
                changeDelay: 500,
                change: function () {
                    var replaced = replaceUrlParam('{{route('showOrganiserHome', ['organiser_id'=>$organiser->id])}}', 'preview_styles', encodeURIComponent($('#OrganiserPageDesign form').serialize()));
                    document.getElementById('previewIframe').src = replaced;
                }
            });

        });
    </script>
@stop

@section('menu')
    @include('ManageOrganiser.Partials.Sidebar')
@stop

@section('page_header')

@stop

@section('content')

    <div class="row">
        <div class="col-md-12">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#organiserSettings" data-toggle="tab">组织者设置</a>
                </li>
                <li>
                    <a href="#OrganiserPageDesign" data-toggle="tab">组织者页面设计</a>
                </li>
            </ul>
            <div class="tab-content panel">
                <div class="tab-pane active" id="organiserSettings">
                    {!! Form::model($organiser, array('url' => route('postEditOrganiser', ['organiser_id' => $organiser->id]), 'class' => 'ajax')) !!}

                    <div class="form-group">
                        {!! Form::label('enable_organiser_page', '开启公共组织者页面', array('class'=>'control-label required')) !!}
                        {!!  Form::select('enable_organiser_page', [
                        '1' => '公开组织者页面.',
                        '0' => '隐藏组织者页面.'],Input::old('enable_organiser_page'),
                                                    array(
                                                    'class'=>'form-control'
                                                    ))  !!}
                        <div class="help-block">
                            组织者页面包含过去和将来活动的公共列表。
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('name', '组织者名称', array('class'=>'required control-label ')) !!}
                        {!!  Form::text('name', Input::old('name'),
                                                array(
                                                'class'=>'form-control'
                                                ))  !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('email', '组织者邮箱', array('class'=>'control-label required')) !!}
                        {!!  Form::text('email', Input::old('email'),
                                                array(
                                                'class'=>'form-control ',
                                                'placeholder'=>''
                                                ))  !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('about', '组织者描述', array('class'=>'control-label ')) !!}
                        {!!  Form::textarea('about', Input::old('about'),
                                                array(
                                                'class'=>'form-control ',
                                                'placeholder'=>'',
                                                'rows' => 4
                                                ))  !!}
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('facebook', '组织者Facebook', array('class'=>'control-label ')) !!}

                                <div class="input-group">
                                    <span style="background-color: #eee;" class="input-group-addon">facebook.com/</span>
                                    {!!  Form::text('facebook', Input::old('facebook'),
                                                    array(
                                                    'class'=>'form-control ',
                                                    'placeholder'=>'Username'
                                                    ))  !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('twitter', '组织者Twitter', array('class'=>'control-label ')) !!}

                                <div class="input-group">
                                    <span style="background-color: #eee;" class="input-group-addon">twitter.com/</span>
                                    {!!  Form::text('twitter', Input::old('twitter'),
                                             array(
                                             'class'=>'form-control ',
                                             'placeholder'=>'Username'
                                             ))  !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    @if(is_file($organiser->logo_path))
                        <div class="form-group">
                            {!! Form::label('current_logo', '当前Logo', array('class'=>'control-label ')) !!}

                            <div class="thumbnail">
                                {!!HTML::image($organiser->logo_path)!!}
                                {!! Form::label('remove_current_image', '删除Logo?', array('class'=>'control-label ')) !!}
                                {!! Form::checkbox('remove_current_image') !!}
                            </div>
                        </div>
                    @endif
                    <div class="form-group">
                        {!!  Form::labelWithHelp('organiser_logo', '组织者Logo', array('class'=>'control-label '),
                            '我们推荐使用方形图，这样在打印票据和活动页面时会更好看。')  !!}
                        {!!Form::styledFile('organiser_logo')!!}
                    </div>
                    <div class="modal-footer">
                        {!! Form::submit('保存组织者', ['class'=>"btn btn-success"]) !!}
                    </div>
                    {!! Form::close() !!}
                </div>
                <div class="tab-pane scale_iframe" id="OrganiserPageDesign">
                    {!! Form::model($organiser, array('url' => route('postEditOrganiserPageDesign', ['event_id' => $organiser->id]), 'class' => 'ajax ')) !!}

                    <div class="row">

                        <div class="col-md-6">
                            <h4>组织者设计</h4>

                            <div class="form-group">
                                {!! Form::label('page_header_bg_color', '头部背景色', ['class'=>'control-label required ']) !!}
                                {!!  Form::input('text', 'page_header_bg_color', Input::old('page_header_bg_color'),
                                                            [
                                                            'class'=>'form-control colorpicker',
                                                            'placeholder'=>'#000000'
                                                            ])  !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('page_text_color', '前景色', ['class'=>'control-label required ']) !!}
                                {!!  Form::input('text', 'page_text_color', Input::old('page_text_color'),
                                                            [
                                                            'class'=>'form-control colorpicker',
                                                            'placeholder'=>'#FFFFFF'
                                                            ])  !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('page_bg_color', '背景色', ['class'=>'control-label required ']) !!}
                                {!!  Form::input('text', 'page_bg_color', Input::old('page_bg_color'),
                                                            [
                                                            'class'=>'form-control colorpicker',
                                                            'placeholder'=>'#EEEEEE'
                                                            ])  !!}
                            </div>
                            <div class="form-group">

                            </div>
                        </div>
                        <div class="col-md-6">
                            <h4>组织者页面预览</h4>
                            <div class="preview iframe_wrap"
                                 style="overflow:hidden; height: 500px; border: 1px solid #ccc; overflow: hidden;">
                                <iframe id="previewIframe"
                                        src="{{ route('showOrganiserHome', ['organiser_id' => $organiser->id]) }}"
                                        frameborder="0" style="overflow:hidden;height:100%;width:100%" width="100%"
                                        height="100%"></iframe>
                            </div>
                        </div>


                    </div>

                    <div class="panel-footer mt15 text-right">
                        {!! Form::submit('保存修改', ['class'=>"btn btn-success"]) !!}
                    </div>

                    {!! Form::close() !!}

                </div>
            </div>
        </div>
@stop


