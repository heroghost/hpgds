<div role="dialog" class="modal fade" style="display: none;">
    {!!  Form::model($question, ['url' => route('postEditEventQuestion', ['event_id' => $event->id, 'question_id' => $question->id]), 'id' => 'edit-question-form', 'class' => 'ajax']) !!}
    <script id="question-option-template" type="text/template">
        <tr>
            <td><input class="form-control" name="option[]" type="text"></td>
            <td width="50">
                <i class="btn btn-danger ico-remove" onclick="removeQuestionOption(this);"></i>
            </td>
        </tr>
    </script>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">
                    <i class="ico-question"></i>
                    编辑调查问题</h3>
            </div>
            <div class="modal-body">
                        <div class="form-group">
                            <label for="question-title" class="required">
                                问题名称
                            </label>
                            {!! Form::text('title', $question->title, [
                                'id' => 'question-title',
                                'class' => 'form-control',
                                'placeholder' => '例如：你的名字是什么？',
                            ]) !!}
                        </div>
                        <div class="form-group">
                            <label for="question-type">
                                选择方式
                            </label>

                            <select id="question-type" class="form-control" name="question_type_id" onchange="changeQuestionType(this);">
                                @foreach ($question_types as $question_type)
                                    <option {{$question->question_type_id == $question_type->id ? 'selected' : ''}} data-has-options="{{$question_type->has_options}}" value="{{$question_type->id}}">
                                        {{$question_type->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <fieldset id="question-options" class="{{ $question->question_type->has_options ? '' : 'hide' }}" >
                            <h4>问题可选项</h4>
                            <table class="table table-condensed table-bordered">
                                <tbody>
                                @if(count($question->options))
                                    @foreach ($question->options as $question_option)
                                        <tr>
                                            <td><input class="form-control" name="option[]" type="text" value="{{ $question_option->name }}"></td>
                                            <td width="50">
                                                <i class="btn btn-danger ico-remove" onclick="removeQuestionOption(this);"></i>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td><input class="form-control" name="option[]" type="text" value=""></td>
                                        <td width="50">

                                        </td>
                                    </tr>
                                    @endif

                                </tbody>
                                <tfoot>
                                    <tr>
                                       <td colspan="2">
                                           <span id="add-question-option" class="btn btn-success btn-xs" onclick="addQuestionOption();">
                                               <i class="ico-plus"></i>
                                               添加其他选项
                                           </span>
                                       </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </fieldset>

                        <div class="form-group">
                            <div class="custom-checkbox ">
                            {!! Form::checkbox('is_required', 1, $question->is_required, ['data-toggle' => 'toggle', 'id' => 'is_required']) !!}
                            {!! Form::label('is_required', '设置必答') !!}
                                </div>
                        </div>

                        <div class="form-group">
                            <label>
                                选择问题的门票:
                            </label>
                            @foreach ($event->tickets as $ticket)
                                <div class="custom-checkbox mb5">
                                <input {{in_array($ticket->id, $question->tickets->lists('id')->toArray()) ? 'checked' : ''}} id="ticket_{{ $ticket->id }}" data-toggle="toggle" name="tickets[]" type="checkbox" value="{{ $ticket->id }}">
                                <label for="ticket_{{ $ticket->id }}">&nbsp; {{ $ticket->title }}</label>
                                    </div>
                            @endforeach
                        </div>



            </div> <!-- /end modal body-->
            <div class="modal-footer">
                {!! Form::button('取消', ['class'=>"btn modal-close btn-danger",'data-dismiss'=>'modal']) !!}
                {!! Form::submit('保存问题', ['class'=>"btn btn-success"]) !!}
            </div>
        </div><!-- /end modal content-->
    </div>
    {!! Form::close() !!}
</div>