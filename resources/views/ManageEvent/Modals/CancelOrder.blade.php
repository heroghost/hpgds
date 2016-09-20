<div role="dialog"  class="modal fade " style="display: none;">
    {!! Form::open(array('url' => route('postCancelOrder', array('order_id' => $order->id)), 'class' => 'closeModalAfter ajax')) !!}
    <script>
        $(function () {
            $('input[name=refund_order]').on('change', function () {
                if ($(this).prop('checked')) {
                    $('.refund_options').slideDown();
                } else {
                    $('.refund_options').slideUp();
                }
            });


        });
    </script>
    <style>
        .refund_options {
            display: none;
        }

        .p0 {
            padding: 0;
        }
    </style>

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">
                    <i class="ico-cart2"></i>
                    取消订单：<b>#{{$order->order_reference}}</b></h3>
            </div>
            <div class="modal-body">

                @if($attendees->count())
                    <div class="help-block">
                        请选择退款者的门票。
                    </div>

                    <div class="well bgcolor-white p0">

                        <div class="table-responsive">
                            <table class="table table-hover ">
                                <tbody>
                                <tr>
                                    <td style="width: 20px;">
                                        <div class="checkbox">
                                            <label>
                                                {!! Form::checkbox('all_attendees', 'on', false, ['class' => 'check-all', 'data-toggle-class'=>'attendee-check']) !!}
                                                <script>
                                                    $(function () {
                                                        $('.check-all').on ('click', function () {
                                                            $('.attendee-check').prop('checked', this.checked);
                                                        });
                                                    });
                                                </script>
                                            </label>
                                        </div>
                                    </td>
                                    <td colspan="3">
                                        全选
                                    </td>
                                </tr>
                                @foreach($attendees as $attendee)

                                    <tr class="{{$attendee->is_cancelled ? 'danger' : ''}}">
                                        <td>
                                            @if(!$attendee->is_cancelled)
                                                {!!Form::checkbox('attendees[]', $attendee->id, false, ['class' => 'attendee-check'])!!}
                                            @endif
                                        </td>
                                        <td>
                                            {{$attendee->first_name}}
                                            {{$attendee->last_name}}
                                        </td>
                                        <td>
                                            {{$attendee->email}}
                                        </td>
                                        <td>
                                            {{{$attendee->ticket->title}}}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                @else
                    <div class="alert alert-info cancelOrderOption">
                        这个订单的所有参与者都被取消了！
                    </div>
                @endif


                @if($order->transaction_id)
                    @if($order->payment_gateway->can_refund)

                <div class="refund_section">
                    @if(!$order->is_refunded)
                        <div>
                            <div class="well bgcolor-white">
                                <div class="checkbox">
                                    <label>
                                        {!!Form::checkbox('refund_order', 'on')!!}
                                        确认退款！
                                    </label>
                                </div>
                            </div>

                            <div class="refund_options">
                                <div class="well bgcolor-white">

                                    <div class="row">
                                        <div class="col-md-1">
                                            <div class="checkbox">
                                                {!!Form::radio('refund_type', 'full', ['selected' => 'selected'])!!}
                                            </div>
                                        </div>
                                        <div class="col-md-11">
                                            <b>全额退款</b>

                                            <div class="help-text">
                                                全额退款：{{(money($order->organiser_amount - $order->amount_refunded, $order->event->currency))}}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="well bgcolor-white clearfix">
                                    <div class="row">
                                        <div class="col-md-1">
                                            <div class="checkbox">
                                                {!!Form::radio('refund_type', 'partial')!!}
                                            </div>
                                        </div>
                                        <div class="col-md-11">
                                            <b>部分退款</b>

                                            <div class="refund_amount">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        退款金额：
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="refund_amount" class="form-control"
                                                               id="refundAmount"
                                                               placeholder="Max {{(money($order->organiser_amount - $order->amount_refunded, $order->event->currency))}}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @else

                        <div class="alert alert-info">
                            {{money($order->amount, $order->event->currency)}} 的订单已经退款
                        </div>

                    @endif
                </div>
                        @else
                        <div class="alert alert-info">
                            对不起，你不能 <b>{{ $order->payment_gateway->provider_name }}</b> 在这里退款。你将不得不在他们的网站上做。
                        </div>
                        @endif

                @endif

            </div>

            @if($attendees->count() || !$order->is_refunded)
                <div class="modal-footer">
                    {!! Form::button('取消', ['class'=>"btn modal-close btn-danger",'data-dismiss'=>'modal']) !!}
                    {!! Form::submit('确认取消订单', ['class'=>"btn btn-primary"]) !!}
                </div>
            @endif
        </div>
        {!! Form::close() !!}
    </div>
</div>
