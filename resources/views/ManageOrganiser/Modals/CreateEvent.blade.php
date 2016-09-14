<div role="dialog"  class="modal fade" style="display: none;">

    @include('ManageOrganiser.Partials.EventCreateAndEditJS');

    {!! Form::open(array('url' => route('postCreateEvent'), 'class' => 'ajax gf')) !!}
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">
                    <i class="ico-calendar"></i>
                    创建活动</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('title', '活动标题', array('class'=>'control-label required')) !!}
                            {!!  Form::text('title', Input::old('title'),array('class'=>'form-control','placeholder'=>'E.g: '.Auth::user()->first_name.'\'s International Conference' ))  !!}
                        </div>

                        <div class="form-group custom-theme">
                            {!! Form::label('description', '活动描述', array('class'=>'control-label required')) !!}
                            {!!  Form::textarea('description', Input::old('description'),
                                        array(
                                        'class'=>'form-control  editable',
                                        'rows' => 5
                                        ))  !!}
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('start_date', '活动起始日期', array('class'=>'required control-label')) !!}
                                    {!!  Form::text('start_date', Input::old('start_date'),
                                                        [
                                                    'class'=>'form-control start hasDatepicker ',
                                                    'data-field'=>'datetime',
                                                    'data-startend'=>'start',
                                                    'data-startendelem'=>'.end',
                                                    'readonly'=>''

                                                ])  !!}
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!!  Form::label('end_date', '活动结束日期',
                                                [
                                            'class'=>'required control-label '
                                        ])  !!}

                                    {!!  Form::text('end_date', Input::old('end_date'),
                                                [
                                            'class'=>'form-control end hasDatepicker ',
                                            'data-field'=>'datetime',
                                            'data-startend'=>'end',
                                            'data-startendelem'=>'.start',
                                            'readonly'=> ''
                                        ])  !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('event_image', '活动图片', array('class'=>'control-label ')) !!}
                            {!! Form::styledFile('event_image') !!}

                        </div>
                        <div class="form-group address-automatic">
                            {!! Form::label('name', '场地名称', array('class'=>'control-label required ')) !!}
                            {!!  Form::text('venue_name_full', Input::old('venue_name_full'),
                                        array(
                                        'class'=>'form-control geocomplete location_field',
                                        'placeholder'=>'例如：螃蟹小屋'
                                        ))  !!}

                                    <!--These are populated with the Google places info-->
                            <div>
                                {!! Form::hidden('formatted_address', '', ['class' => 'location_field']) !!}
                                {!! Form::hidden('street_number', '', ['class' => 'location_field']) !!}
                                {!! Form::hidden('country', '', ['class' => 'location_field']) !!}
                                {!! Form::hidden('country_short', '', ['class' => 'location_field']) !!}
                                {!! Form::hidden('place_id', '', ['class' => 'location_field']) !!}
                                {!! Form::hidden('name', '', ['class' => 'location_field']) !!}
                                {!! Form::hidden('location', '', ['class' => 'location_field']) !!}
                                {!! Form::hidden('postal_code', '', ['class' => 'location_field']) !!}
                                {!! Form::hidden('route', '', ['class' => 'location_field']) !!}
                                {!! Form::hidden('lat', '', ['class' => 'location_field']) !!}
                                {!! Form::hidden('lng', '', ['class' => 'location_field']) !!}
                                {!! Form::hidden('administrative_area_level_1', '', ['class' => 'location_field']) !!}
                                {!! Form::hidden('sublocality', '', ['class' => 'location_field']) !!}
                                {!! Form::hidden('locality', '', ['class' => 'location_field']) !!}
                            </div>
                            <!-- /These are populated with the Google places info-->
                        </div>

                        <div class="address-manual" style="display:none;">
                            <h5>
                                地址详情
                            </h5>

                            <div class="form-group">
                                {!! Form::label('location_venue_name', '场地名称', array('class'=>'control-label required ')) !!}
                                {!!  Form::text('location_venue_name', Input::old('location_venue_name'), [
                                        'class'=>'form-control location_field',
                                        'placeholder'=>'E.g: The Crab Shack'
                                        ])  !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('location_address_line_1', '地址线路1', array('class'=>'control-label')) !!}
                                {!!  Form::text('location_address_line_1', Input::old('location_address_line_1'), [
                                        'class'=>'form-control location_field',
                                        'placeholder'=>'E.g: 45 Grafton St.'
                                        ])  !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('location_address_line_2', '地址线路2', array('class'=>'control-label')) !!}
                                {!!  Form::text('location_address_line_2', Input::old('location_address_line_2'), [
                                        'class'=>'form-control location_field',
                                        'placeholder'=>'例如：邓布林.'
                                        ])  !!}
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('location_state', '城市', array('class'=>'control-label')) !!}
                                        {!!  Form::text('location_state', Input::old('location_state'), [
                                                'class'=>'form-control location_field',
                                                'placeholder'=>'例如：邓布林.'
                                                ])  !!}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('location_post_code', '邮编', array('class'=>'control-label')) !!}
                                        {!!  Form::text('location_post_code', Input::old('location_post_code'), [
                                                'class'=>'form-control location_field',
                                                'placeholder'=>'例如：邓布林.'
                                                ])  !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <span>
                            <a data-clear-field=".location_field"
                               data-toggle-class=".address-automatic, .address-manual"
                               data-show-less-text="or <b>Select from existing venues</b>" href="javascript:void(0);"
                               class="in-form-link show-more-options clear_location">
                                or <b>手动输入地址</b>
                            </a>
                        </span>

                        @if($organiser_id)
                            {!! Form::hidden('organiser_id', $organiser_id) !!}
                        @else
                            <div class="create_organiser" style="{{$organisers->isEmpty() ? '' : 'display:none;'}}">
                                <h5>
                                    组织者详情
                                </h5>

                                <div class="form-group">
                                    {!! Form::label('organiser_name', '组织者名称', array('class'=>'required control-label ')) !!}
                                    {!!  Form::text('organiser_name', Input::old('organiser_name'),
                                                array(
                                                'class'=>'form-control',
                                                'placeholder'=>'谁组织了这个活动?'
                                                ))  !!}
                                </div>
                                <div class="form-group">
                                    {!! Form::label('organiser_email', 'Organiser Email', array('class'=>'control-label required')) !!}
                                    {!!  Form::text('organiser_email', Input::old('organiser_email'),
                                                array(
                                                'class'=>'form-control ',
                                                'placeholder'=>''
                                                ))  !!}
                                </div>
                                <div class="form-group">
                                    {!! Form::label('organiser_about', 'Organizer Description', array('class'=>'control-label ')) !!}
                                    {!!  Form::textarea('organiser_about', Input::old('organiser_about'),
                                                array(
                                                'class'=>'form-control editable2',
                                                'placeholder'=>'',
                                                'rows' => 4
                                                ))  !!}
                                </div>
                                <div class="form-group more-options">
                                    {!! Form::label('organiser_logo', 'Organiser Logo', array('class'=>'control-label ')) !!}
                                    {!! Form::styledFile('organiser_logo') !!}
                                </div>
                                <div class="row more-options">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label('organiser_facebook', 'Organiser Facebook', array('class'=>'control-label ')) !!}
                                            {!!  Form::text('organiser_facebook', Input::old('organiser_facebook'),
                                                array(
                                                'class'=>'form-control ',
                                                'placeholder'=>'E.g http://www.facebook.com/MyFaceBookPage'
                                                ))  !!}

                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label('organiser_twitter', 'Organiser Twitter', array('class'=>'control-label ')) !!}
                                            {!!  Form::text('organiser_twitter', Input::old('organiser_twitter'),
                                                array(
                                                'class'=>'form-control ',
                                                'placeholder'=>'E.g http://www.twitter.com/MyTwitterPage'
                                                ))  !!}

                                        </div>
                                    </div>
                                </div>

                                <a data-show-less-text="Hide Additional Oraganiser Options" href="javascript:void(0);"
                                   class="in-form-link show-more-options">
                                    更多组织者信息
                                </a>
                            </div>

                            @if(!$organisers->isEmpty())
                                <div class="form-group select_organiser" style="{{$organisers ? '' : 'display:none;'}}">

                                    {!! Form::label('organiser_id', '选择组织者', array('class'=>'control-label ')) !!}
                                    {!! Form::select('organiser_id', $organisers, $organiser_id, ['class' => 'form-control']) !!}

                                </div>
                                <span class="">
                                    <a data-toggle-class=".select_organiser, .create_organiser"
                                       data-show-less-text="or <b>Select an organiser</b>" href="javascript:void(0);"
                                       class="in-form-link show-more-options">
                                        or <b>创建一个组织者</b>
                                    </a>
                                </span>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <span class="uploadProgress"></span>
                {!! Form::button('Cancel', ['class'=>"btn modal-close btn-danger",'data-dismiss'=>'modal']) !!}
                {!! Form::submit('创建活动', ['class'=>"btn btn-success"]) !!}
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>
