@extends('Shared.Layouts.BlankSlate')

@section('blankslate-icon-class')
    ico-question2
@stop

@section('blankslate-title')
    还没有添加问题
@stop

@section('blankslate-text')
    你可以在这里添加问题，当参与者完成活动时，可以提问
@stop

@section('blankslate-body')
    <button data-invoke="modal" data-modal-id='CreateQuestion' data-href="{{route('showCreateEventQuestion', array('event_id'=>$event->id))}}" href='javascript:void(0);'  class=' btn btn-success mt5 btn-lg' type="button" >
        <i class="ico-question"></i>
        创建问题
    </button>
@stop


