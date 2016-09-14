{!! HTML::style(asset('assets/stylesheet/ticket.css')) !!}
<style>
    .ticket {
        border: 1px solid {{$event->ticket_border_color}};
        background: {{$event->ticket_bg_color}} ;
        color: {{$event->ticket_sub_text_color}};
        border-left-color: {{$event->ticket_border_color}} ;
    }
    .ticket h4 {color: {{$event->ticket_text_color}};}
    .ticket .logo {
        border-left: 1px solid {{$event->ticket_border_color}};
        border-bottom: 1px solid {{$event->ticket_border_color}};

    }
</style>
<div class="ticket">
    <div class="logo">
        {!! HTML::image(asset($image_path)) !!}
    </div>

    <div class="event_details">
        <h4>活动</h4>Demo活动<h4>组织者</h4>Demo组织者<h4>场地</h4>Demo地点<h4>开始日期 / 时间</h4>
        Mar 18th 4:08PM
        <h4>结束日期 / 时间</h4>
        Mar 18th 5:08PM
    </div>

    <div class="attendee_details">
        <h4>名称</h4>Bill 博客<h4>门票类型</h4>
        普通入场费
        <h4>订单编号.</h4>
        #YLY9U73
        <h4>参加者编号.</h4>
        #YLY9U73-1
        <h4>价格</h4>
        €XX.XX
    </div>

    <div class="barcode">
        {!! DNS2D::getBarcodeSVG('hello', "QRCODE", 6, 6) !!}
    </div>
    @if($event->is_1d_barcode_enabled)
        <div class="barcode_vertical">
            {!! DNS1D::getBarcodeSVG(12211221, "C39+", 1, 50) !!}
        </div>
    @endif
</div>
