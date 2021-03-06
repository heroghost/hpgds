@extends('Shared.Layouts.Master')

@section('title')
    @parent
    活动门票
@stop

@section('top_nav')
    @include('ManageEvent.Partials.TopNav')
@stop

@section('page_title')
    <i class="ico-ticket mr5"></i>
    活动门票
@stop


@section('head')
    <script>
        $(function () {


            $('.sortable').sortable({
                handle: '.sortHanlde',
                forcePlaceholderSize: true,
                placeholderClass: 'col-md-4 col-sm-6 col-xs-12',
            }).bind('sortupdate', function (e, ui) {

                var data = $('.sortable tr').map(function () {
                    return $(this).data('question-id');
                }).get();

                $.ajax({
                    type: 'POST',
                    url: '',//Attendize.postUpdateTicketsOrderRoute,
                    dataType: 'json',
                    data: {question_ids: data},
                    success: function (data) {
                        showMessage(data.message)
                    },
                    error: function (data) {
                        console.log(data);
                    }
                });
            });
        });
    </script>
    @stop

@section('menu')
    @include('ManageEvent.Partials.Sidebar')
@stop

@section('page_header')
    <div class="col-md-9">
        <!-- Toolbar -->
        <div class="btn-toolbar" role="toolbar">
            <div class="btn-group btn-group-responsive">
                <button data-modal-id='CreateTicket'
                        data-href="{{route('showCreateTicket', array('event_id'=>$event->id))}}"
                        class='loadModal btn btn-success' type="button"><i class="ico-ticket"></i> 创建门票
                </button>
            </div>
            @if(false)
                <div class="btn-group btn-group-responsive ">
                    <button data-modal-id='TicketQuestions'
                            data-href="{{route('showTicketQuestions', array('event_id'=>$event->id))}}" type="button"
                            class="loadModal btn btn-success">
                        <i class="ico-question"></i> 问题
                    </button>
                </div>
                <div class="btn-group btn-group-responsive">
                    <button type="button" class="btn btn-success">
                        <i class="ico-tags"></i> 优惠券编码
                    </button>
                </div>
            @endif
        </div>
        <!--/ Toolbar -->
    </div>
    <div class="col-md-3">
        {!! Form::open(array('url' => route('showEventTickets', ['event_id'=>$event->id,'sort_by'=>$sort_by]), 'method' => 'get')) !!}
        <div class="input-group">
            <input name='q' value="{{$q or ''}}" placeholder="搜索门票..." type="text" class="form-control">
        <span class="input-group-btn">
            <button class="btn btn-default" type="submit"><i class="ico-search"></i></button>
        </span>
            {!!Form::hidden('sort_by', $sort_by)!!}
        </div>
        {!! Form::close() !!}
    </div>
@stop

@section('content')
    @if($tickets->count())

        <div class="row">
            <div class="col-md-3 col-xs-6">
                <div class='order_options'>
                    <span class="event_count">{{$tickets->count()}} 活动数目</span>
                </div>
            </div>
            <div class="col-md-2 col-xs-6 col-md-offset-7">
                <div class='order_options'>
                    {!! Form::select('sort_by_select', $allowed_sorts, $sort_by, ['class' => 'form-control pull right']) !!}
                </div>
            </div>
        </div>
        @endif
                <!--Start ticket table-->
        <div class="row sortable">
            @if($tickets->count())

                @foreach($tickets as $ticket)
                    <div id="ticket_{{$ticket->id}}" class="col-md-4 col-sm-6 col-xs-12 ">
                        <div class="panel panel-success ticket">

                            <div style="cursor: pointer;" data-modal-id='ticket-{{ $ticket->id }}'
                                 data-href="{{ route('showEditTicket', ['event_id' => $event->id, 'ticket_id' => $ticket->id]) }}"
                                 class="panel-heading loadModal">
                                <h3 class="panel-title">
                                    <i class="ico-ticket ticket_icon mr5 ellipsis"></i>
                                    {{$ticket->title}}
                                    <span class="pull-right">
                        {{ ($ticket->is_free) ? "FREE" : money($ticket->price, $event->currency) }}
                    </span>
                                </h3>
                            </div>

                            <div class='panel-body'>
                                <ul class="nav nav-section nav-justified mt5 mb5">
                                    <li>
                                        <div class="section">
                                            <h4 class="nm">{{ $ticket->quantity_sold }}</h4>

                                            <p class="nm text-muted">已售</p>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="section">
                                            <h4 class="nm">
                                                {{ ($ticket->quantity_available === null) ? '&infin;' : $ticket->quantity_remaining }}
                                            </h4>

                                            <p class="nm text-muted">总数目</p>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="section">
                                            <h4 class="nm hint--top"
                                                title="{{money($ticket->sales_volume, $event->currency)}} + {{money($ticket->organiser_fees_volume, $event->currency)}} Organiser Booking Fees">
                                                {{money($ticket->sales_volume + $ticket->organiser_fees_volume, $event->currency)}}
                                                <sub title="Doesn't account for refunds.">*</sub>
                                            </h4>

                                            <p class="nm text-muted">收入</p>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div class="panel-footer sortHandle" style="height: 56px;">
                                <ul class="nav nav-section nav-justified">
                                    <li>
                                        <a href="javascript:void(0);" style="cursor: move;">
                                            @if($ticket->sale_status === config('attendize.ticket_status_on_sale'))
                                                @if($ticket->is_paused)
                                                    暂停门票售卖 &nbsp;
                                                    <span class="pauseTicketSales label label-info"  style="cursor: auto;"
                                                          data-id="{{$ticket->id}}"
                                                          data-route="{{route('postPauseTicket', ['event_id'=>$event->id])}}">
                                    <i class="ico-play4"></i> 启动
                                </span>
                                                @else
                                                    售卖中 &nbsp;
                                                    <span class="pauseTicketSales label label-info"  style="cursor: auto;"
                                                          data-id="{{$ticket->id}}"
                                                          data-route="{{route('postPauseTicket', ['event_id'=>$event->id])}}">
                                    <i class="ico-pause"></i> 暂停
                                </span>
                                                @endif
                                            @else
                                                {{\App\Models\TicketStatus::find($ticket->sale_status)->name}}
                                            @endif
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                @endforeach

            @else

                @if($q)
                    @include('Shared.Partials.NoSearchResults')
                @else
                    @include('ManageEvent.Partials.TicketsBlankSlate')
                @endif


            @endif
        </div><!--/ end ticket table-->

        <div class="row">
            <div class="col-md-12">
                {!! $tickets->appends(['q' => $q, 'sort_by' => $sort_by])->render() !!}
            </div>
        </div>
@stop

