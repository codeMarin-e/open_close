@php
    $translationsDefault = trans('admin/open_close/open_close');
    $langs = isset($translations)?
        transOrOther($translations, 'admin/open_close/open_close', array_keys($translationsDefault)) : $translationsDefault;
    $openClose_bag = $openClose_bag ?? 'open_close';
    $openCloseSpecial_bag = $openCloseSpecial_bag?? 'open_close_special';
    $weekDays = [
        1 => $langs['monday'],
        2 => $langs['tuesday'],
        3 => $langs['wednesday'],
        4 => $langs['thursday'],
        5 => $langs['friday'],
        6 => $langs['saturday'],
        7 => $langs['sunday'],
    ];
    $nowDT = new \Datetime();
@endphp

@pushonce('above_css')
<!-- JQUERY UI -->
<link href="{{ asset('admin/vendor/jquery-ui-1.12.1/jquery-ui.min.css') }}" rel="stylesheet" type="text/css" />
@endpushonce

@pushonce('below_js')
<script language="javascript"
        type="text/javascript"
        src="{{ asset('admin/vendor/jquery-ui-1.12.1/jquery-ui.min.js') }}"></script>
@endpushonce

@pushonceOnReady('below_js_on_ready')
<script>
    var $specialOpenCloseTemplate = $('#js_special_open_close_template').html();
    var $specialOpenCloseCon = $('#js_special_open_con');
    var specialOpenCloseIndex = $specialOpenCloseCon.find('.js_special_open_close').length-1;
    $(document).on('click', '#js_add_special_open_close', function(e) {
        e.preventDefault();
        var newIndex = specialOpenCloseIndex+1;
        var template =$specialOpenCloseTemplate
            .replace(/__INDEX__/g, newIndex)
            .replace(/__DATEPICKER__/g, 'datepicker');
        $specialOpenCloseCon.append( template );
        specialOpenCloseIndex = newIndex;
        $(document).trigger('datePickersInstance');
    });
    $(document).on('click', '.js_special_open_close_remove', function(e) {
        e.preventDefault();
        var $this = $(this);
        if(!confirm($this.attr("data-remove_ask"))) return false;
        var $parent = $this.parents('.js_special_open_close').first();
        $parent.remove();
    });

    $(document).on('datePickersInstance', function() {
        $('.datepicker:not(.js_initiated)').datepicker({
            dateFormat: "dd.mm.yy"
        }).addClass('js_initiated');
    });
    $(document).trigger('datePickersInstance');
</script>
@endpushonceOnReady

@pushonce('below_templates')
<form id="js_special_open_close_template" class="d-none">
    <div class="form-inline mb-3 mt-2 js_special_open_close" >
        <div class="form-group w-20">
            <input class="form-control __DATEPICKER__ w-100"
                   name='{{$inputBag}}[{{$openCloseSpecial_bag}}][__INDEX__][date]'
                   id='{{$inputBag}}[{{$openCloseSpecial_bag}}][__INDEX__][date]'
                   placeholder="Date"
            />
        </div>
        <div class="form-group mr-1">
            <select class="form-control"
                    name='{{$inputBag}}[{{$openCloseSpecial_bag}}][__INDEX__][open_at][hour]'
                    id='{{$inputBag}}[{{$openCloseSpecial_bag}}][__INDEX__][open_at][hour]'>
                @for($i=0;$i<24;$i++)
                    <option value='{{ sprintf('%02d', $i) }}'>{{ sprintf('%02d', $i) }}</option>
                @endfor
            </select>
        </div> :
        <div class="form-group ml-1">
            <select class="form-control"
                    name='{{$inputBag}}[{{$openCloseSpecial_bag}}][__INDEX__][open_at][minute]'
                    id='{{$inputBag}}[{{$openCloseSpecial_bag}}][__INDEX__][open_at][minute]'>
                @for($i=0;$i<60;$i++)
                    <option value='{{ sprintf('%02d', $i) }}'>{{ sprintf('%02d', $i) }}</option>
                @endfor
            </select>
        </div>
        &nbsp;-&nbsp;
        <div class="form-group mr-1">
            <select class="form-control"
                    name='{{$inputBag}}[{{$openCloseSpecial_bag}}][__INDEX__][close_at][hour]'
                    id='{{$inputBag}}[{{$openCloseSpecial_bag}}][__INDEX__][close_at][hour]'>
                @for($i=0;$i<24;$i++)
                    <option value='{{ sprintf('%02d', $i) }}'>{{ sprintf('%02d', $i) }}</option>
                @endfor
            </select>
        </div> :
        <div class="form-group ml-1">
            <select class="form-control"
                    name='{{$inputBag}}[{{$openCloseSpecial_bag}}][__INDEX__][close_at][minute]'
                    id='{{$inputBag}}[{{$openCloseSpecial_bag}}][__INDEX__][close_at][minute]'>
                @for($i=0;$i<60;$i++)
                    <option value='{{ sprintf('%02d', $i) }}'>{{ sprintf('%02d', $i) }}</option>
                @endfor
            </select>
        </div>

        <div class="form-group ml-2">
            <div class="form-check form-check-inline">
                <input type="checkbox"
                       value="1"
                       id="{{$inputBag}}[{{$openCloseSpecial_bag}}][__INDEX__][close_next_day]"
                       name="{{$inputBag}}[{{$openCloseSpecial_bag}}][__INDEX__][close_next_day]"
                       class="form-check-input"
                />
                <label class="form-check-label align-middle" for="{{$inputBag}}[{{$openCloseSpecial_bag}}][__INDEX__][close_next_day]">{{$langs['next_day']}}</label>
            </div>
        </div>
        <div class="form-group ml-2">
            <a href="#" class="js_special_open_close_remove text-danger"
               title="{{$langs['remove']}}"
               data-remove_ask="{{$langs['remove_ask']}}">X</a>
        </div>
    </div>
</form>
@endpushonce
@isset($openCloseable)
    <div class="row">
        <h5 class="col-lg-7">{{-- $langs['now'] --}} [{{$nowDT->format('d.m.Y H:i')}}]:
            @if($openCloseable->isOpen($nowDT))<span class="font-weight-bold text-success">{{$langs['opened']}}</span>
            @else<span class="font-weight-bold text-danger">{{$langs['closed']}}</span>@endif
        </h5>
        <div class="col-lg-5">
            <button type="button"
                    id="js_add_special_open_close"
                    class="btn btn-sm btn-success"><i class="fa fa-plus-circle"></i> {{$langs['add_special']}}</button>
        </div>
    </div>
@endisset
<fieldset class="row">
    <div class="col-lg-6">
        @foreach($weekDays as $weekDayIndex => $dayIndexName)
            <div class="form-inline mb-3 mt-2">
                @php
                    if($openCloseableWeekDay = (isset($openCloseable)? $openCloseable->getWeekDaysOpenClose($weekDayIndex) : null)) {
                        $openCloseableWeekDayOpen = $openCloseableWeekDay->open_at? new \DateInterval($openCloseableWeekDay->open_at) : null;
                        $openCloseableWeekDayClose = $openCloseableWeekDay->close_at? new \DateInterval($openCloseableWeekDay->close_at) : null;
                    }
                    $sPFhour = old("{$inputBag}.{$openClose_bag}.{$weekDayIndex}.open_at.hour", (($openCloseableWeekDay && $openCloseableWeekDayOpen)? $openCloseableWeekDayOpen->h : 0));
                    $sPThour = old("{$inputBag}.{$openClose_bag}.{$weekDayIndex}.close_at.hour",  (($openCloseableWeekDay && $openCloseableWeekDayClose)? $openCloseableWeekDayClose->h : 0));
                    $sPFminute = old("{$inputBag}.{$openClose_bag}.{$weekDayIndex}.open_at.minute", (($openCloseableWeekDay && $openCloseableWeekDayOpen)? $openCloseableWeekDayOpen->i : 0));
                    $sPTminute = old("{$inputBag}.{$openClose_bag}.{$weekDayIndex}.close_at.minute", (($openCloseableWeekDay && $openCloseableWeekDayClose)? $openCloseableWeekDayClose->i : 0));
                    $cSDclosedNextDay = old("{$inputBag}.{$openClose_bag}.{$weekDayIndex}.close_next_day", (($openCloseableWeekDay)? $openCloseableWeekDay->close_next_day : false));
                @endphp
                <div class="form-group w-15">
                    <div class="mr-2 col-form-label">{{$dayIndexName}}:</div>
                </div>
                <div class="form-group mr-1">
                    <select class="form-control @if($errors->$inputBag->has("{$openClose_bag}.{$weekDayIndex}.open_at.hour")) is-invalid @endif"
                            name='{{$inputBag}}[{{$openClose_bag}}][{{$weekDayIndex}}][open_at][hour]'
                            id='{{$inputBag}}[{{$openClose_bag}}][{{$weekDayIndex}}][open_at][hour]'>
                        @for($i=0;$i<24;$i++)
                            <option value='{{ sprintf('%02d', $i) }}'
                                    @if($sPFhour == $i)selected="selected"@endif>{{ sprintf('%02d', $i) }}</option>
                        @endfor
                    </select>
                </div> :
                <div class="form-group mx-1">
                    <select class="form-control @if($errors->$inputBag->has("{$openClose_bag}.{$weekDayIndex}.open_at.minute")) is-invalid @endif"
                            name='{{$inputBag}}[{{$openClose_bag}}][{{$weekDayIndex}}][open_at][minute]'
                            id='{{$inputBag}}[{{$openClose_bag}}][{{$weekDayIndex}}][open_at][minute]'>
                        @for($i=0;$i<60;$i++)
                            <option value='{{ sprintf('%02d', $i) }}'
                                    @if($sPFminute == $i)selected="selected"@endif>{{ sprintf('%02d', $i) }}</option>
                        @endfor
                    </select>
                </div>
                &nbsp;-&nbsp;
                <div class="form-group mr-1">
                    <select class="form-control @if($errors->$inputBag->has("{$openClose_bag}.{$weekDayIndex}.close_at.hour")) is-invalid @endif"
                            name='{{$inputBag}}[{{$openClose_bag}}][{{$weekDayIndex}}][close_at][hour]'
                            id='{{$inputBag}}[{{$openClose_bag}}][{{$weekDayIndex}}][close_at][hour]'
                    >
                        @for($i=0;$i<24;$i++)
                            <option value='{{ sprintf('%02d', $i) }}'
                                    @if($sPThour == $i)selected="selected"@endif>{{ sprintf('%02d', $i) }}</option>
                        @endfor
                    </select>
                </div> :
                <div class="form-group ml-1">
                    <select class="form-control @if($errors->$inputBag->has("{$openClose_bag}.{$weekDayIndex}.close_at.minute")) is-invalid @endif"
                            name='{{$inputBag}}[{{$openClose_bag}}][{{$weekDayIndex}}][close_at][minute]'
                            id='{{$inputBag}}[{{$openClose_bag}}][{{$weekDayIndex}}][close_at][minute]'>
                        @for($i=0;$i<60;$i++)
                            <option value='{{ sprintf('%02d', $i) }}'
                                    @if($sPTminute == $i)selected="selected"@endif>{{ sprintf('%02d', $i) }}</option>
                        @endfor
                    </select>
                </div>

                <div class="form-group ml-1">
                    <div class="form-check form-check-inline">
                        <input type="checkbox"
                               value="1"
                               id="{{$inputBag}}[{{$openClose_bag}}][{{$weekDayIndex}}][close_next_day]"
                               name="{{$inputBag}}[{{$openClose_bag}}][{{$weekDayIndex}}][close_next_day]"
                               class="form-check-input @if($errors->$inputBag->has("{$openClose_bag}.{$weekDayIndex}.close_next_day")) is-invalid @endif"
                               @if($openCloseableWeekDay && $openCloseableWeekDay->close_next_day) checked="checked" @endif
                        />
                        <label class="form-check-label align-middle" for="{{$inputBag}}[{{$openClose_bag}}][{{$weekDayIndex}}][close_next_day]">{{$langs['next_day']}}</label>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- SPECIAL DATES --}}
    <div class="col-lg-6" id="js_special_open_con">
        @if($openCloseable)
            @if(old("{$inputBag}.{$openCloseSpecial_bag}"))
                {{-- OLD SPECIAL DATES --}}
                @foreach(old("{$inputBag}.{$openCloseSpecial_bag}") as $specialDay)
                    <div class="form-inline mb-3 mt-2 js_special_open_close">
                        @php
                            $sPFhour = old("{$inputBag}.{$openCloseSpecial_bag}.{$loop->index}.open_at.hour", 0);
                            $sPThour = old("{$inputBag}.{$openCloseSpecial_bag}.{$loop->index}.close_at.hour", 0);
                            $sPFminute = old("{$inputBag}.{$openCloseSpecial_bag}.{$loop->index}.open_at.minute",0);
                            $sPTminute = old("{$inputBag}.{$openCloseSpecial_bag}.{$loop->index}.close_at.minute",0);
                            $cSDclosedNextDay = old("{$inputBag}.{$openCloseSpecial_bag}.{$loop->index}.close_next_day", false);
                        @endphp
                        <div class="form-group w-20">
                            <input class="form-control datepicker w-100 @if($errors->$inputBag->has("{$openCloseSpecial_bag}.{$loop->index}.date")) is-invalid @endif"
                                   name='{{$inputBag}}[{{$openCloseSpecial_bag}}][{{$loop->index}}][date]'
                                   id='{{$inputBag}}[{{$openCloseSpecial_bag}}][{{$loop->index}}][date]'
                                   placeholder="Date"
                                   value="{{old("{$inputBag}.{$openCloseSpecial_bag}.{$loop->index}.date", '')}}"
                            />
                        </div>
                        <div class="form-group mr-1">
                            <select class="form-control @if($errors->$inputBag->has("{$openCloseSpecial_bag}.{$loop->index}.open_at.hour")) is-invalid @endif"
                                    name='{{$inputBag}}[{{$openCloseSpecial_bag}}][{{$loop->index}}][open_at][hour]'
                                    id='{{$inputBag}}[{{$openCloseSpecial_bag}}][{{$loop->index}}][open_at][hour]'>
                                @for($i=0;$i<24;$i++)
                                    <option value='{{ sprintf('%02d', $i) }}'
                                            @if($sPFhour == $i)selected="selected"@endif>{{ sprintf('%02d', $i) }}</option>
                                @endfor
                            </select>
                        </div> :
                        <div class="form-group ml-1">
                            <select class="form-control @if($errors->$inputBag->has("{$openCloseSpecial_bag}.{$loop->index}.open_at.minute")) is-invalid @endif"
                                    name='{{$inputBag}}[{{$openCloseSpecial_bag}}][{{$loop->index}}][open_at][minute]'
                                    id='{{$inputBag}}[{{$openCloseSpecial_bag}}][{{$loop->index}}][open_at][minute]'>
                                @for($i=0;$i<60;$i++)
                                    <option value='{{ sprintf('%02d', $i) }}'
                                            @if($sPFminute == $i)selected="selected"@endif>{{ sprintf('%02d', $i) }}</option>
                                @endfor
                            </select>
                        </div>
                        &nbsp;-&nbsp;
                        <div class="form-group mr-1">
                            <select class="form-control @if($errors->$inputBag->has("{$openCloseSpecial_bag}.{$loop->index}.close_at.hour")) is-invalid @endif"
                                    name='{{$inputBag}}[{{$openCloseSpecial_bag}}][{{$loop->index}}][close_at][hour]'
                                    id='{{$inputBag}}[{{$openCloseSpecial_bag}}][{{$loop->index}}][close_at][hour]'>
                                @for($i=0;$i<24;$i++)
                                    <option value='{{ sprintf('%02d', $i) }}'
                                            @if($sPThour == $i)selected="selected"@endif>{{ sprintf('%02d', $i) }}</option>
                                @endfor
                            </select>
                        </div> :
                        <div class="form-group ml-1">
                            <select class="form-control @if($errors->$inputBag->has("{$openCloseSpecial_bag}.{$loop->index}.close_at.minute")) is-invalid @endif"
                                    name='{{$inputBag}}[{{$openCloseSpecial_bag}}][{{$loop->index}}][close_at][minute]'
                                    id='{{$inputBag}}[{{$openCloseSpecial_bag}}][{{$loop->index}}][close_at][minute]'>
                                @for($i=0;$i<60;$i++)
                                    <option value='{{ sprintf('%02d', $i) }}'
                                            @if($sPTminute == $i)selected="selected"@endif>{{ sprintf('%02d', $i) }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="form-group ml-1">
                            <div class="form-check form-check-inline">
                                <input type="checkbox"
                                       value="1"
                                       id="{{$inputBag}}[{{$openCloseSpecial_bag}}][{{$loop->index}}][close_next_day]"
                                       name="{{$inputBag}}[{{$openCloseSpecial_bag}}][{{$loop->index}}][close_next_day]"
                                       class="form-check-input @if($errors->$inputBag->has("{$openCloseSpecial_bag}.{$loop->index}.close_next_day")) is-invalid @endif"
                                       @if($cSDclosedNextDay) checked="checked" @endif
                                />
                                <label class="form-check-label align-middle" for="{{$inputBag}}[{{$openCloseSpecial_bag}}][{{$loop->index}}][close_next_day]">{{$langs['next_day']}}</label>
                            </div>
                        </div>

                        <div class="form-group ml-1">
                            <a href="#" class="js_special_open_close_remove text-danger"
                               title="{{$langs['remove']}}"
                               data-remove_ask="{{$langs['remove_ask']}}">X</a>
                        </div>
                    </div>
                @endforeach
                {{-- END OLD SPECIAL DATES --}}
            @else
                {{-- OBEJECT SPECIAL DATES --}}
                @foreach($openCloseable->open_closes_special()->get() as $specialDay)
                    <div class="form-inline mb-3 mt-2 js_special_open_close">
                        @php
                            $openCloseableWeekDayOpen = $specialDay->open_at? new \DateInterval($specialDay->open_at) : null;
                            $openCloseableWeekDayClose = $specialDay->close_at? new \DateInterval($specialDay->close_at) : null;
                            $sPFhour = ( $openCloseableWeekDayOpen)? $openCloseableWeekDayOpen->h : 0;
                            $sPThour = ($openCloseableWeekDayClose)? $openCloseableWeekDayClose->h : 0;
                            $sPFminute = ($openCloseableWeekDayOpen)? $openCloseableWeekDayOpen->i : 0;
                            $sPTminute = ($openCloseableWeekDayClose)? $openCloseableWeekDayClose->i : 0;
                            $cSDclosedNextDay = (boolean)$specialDay->close_next_day;
                            $specialDayTypeValue = new \DateTime();
                            $specialDayTypeValue->setTimestamp((int)$specialDay->type_value);
                        @endphp
                        <div class="form-group w-20">
                            <input class="form-control datepicker w-100"
                                   name='{{$inputBag}}[{{$openCloseSpecial_bag}}][{{$loop->index}}][date]'
                                   id='{{$inputBag}}[{{$openCloseSpecial_bag}}][{{$loop->index}}][date]'
                                   placeholder="Date"
                                   value="{{$specialDayTypeValue->format('d.m.Y')}}"
                            />
                        </div>
                        <div class="form-group mr-1">
                            <select class="form-control"
                                    name='{{$inputBag}}[{{$openCloseSpecial_bag}}][{{$loop->index}}][open_at][hour]'
                                    id='{{$inputBag}}[{{$openCloseSpecial_bag}}][{{$loop->index}}][open_at][hour]'>
                                @for($i=0;$i<24;$i++)
                                    <option value='{{ sprintf('%02d', $i) }}'
                                            @if($sPFhour == $i)selected="selected"@endif>{{ sprintf('%02d', $i) }}</option>
                                @endfor
                            </select>
                        </div> :
                        <div class="form-group mx-1">
                            <select class="form-control"
                                    name='{{$inputBag}}[{{$openCloseSpecial_bag}}][{{$loop->index}}][open_at][minute]'
                                    id='{{$inputBag}}[{{$openCloseSpecial_bag}}][{{$loop->index}}][open_at][minute]'>
                                @for($i=0;$i<60;$i++)
                                    <option value='{{ sprintf('%02d', $i) }}'
                                            @if($sPFminute == $i)selected="selected"@endif>{{ sprintf('%02d', $i) }}</option>
                                @endfor
                            </select>
                        </div>
                        &nbsp;-&nbsp;
                        <div class="form-group mr-1">
                            <select class="form-control"
                                    name='{{$inputBag}}[{{$openCloseSpecial_bag}}][{{$loop->index}}][close_at][hour]'
                                    id='{{$inputBag}}[{{$openCloseSpecial_bag}}][{{$loop->index}}][close_at][hour]'>
                                @for($i=0;$i<24;$i++)
                                    <option value='{{ sprintf('%02d', $i) }}'
                                            @if($sPThour == $i)selected="selected"@endif>{{ sprintf('%02d', $i) }}</option>
                                @endfor
                            </select>
                        </div> :
                        <div class="form-group ml-1">
                            <select class="form-control"
                                    name='{{$inputBag}}[{{$openCloseSpecial_bag}}][{{$loop->index}}][close_at][minute]'
                                    id='{{$inputBag}}[{{$openCloseSpecial_bag}}][{{$loop->index}}][close_at][minute]'>
                                @for($i=0;$i<60;$i++)
                                    <option value='{{ sprintf('%02d', $i) }}'
                                            @if($sPTminute == $i)selected="selected"@endif>{{ sprintf('%02d', $i) }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="form-group ml-1">
                            <div class="form-check form-check-inline">
                                <input type="checkbox"
                                       value="1"
                                       id="{{$inputBag}}[{{$openCloseSpecial_bag}}][{{$loop->index}}][close_next_day]"
                                       name="{{$inputBag}}[{{$openCloseSpecial_bag}}][{{$loop->index}}][close_next_day]"
                                       class="form-check-input"
                                       @if($cSDclosedNextDay) checked="checked" @endif
                                />
                                <label class="form-check-label align-middle" for="{{$inputBag}}[{{$openCloseSpecial_bag}}][{{$loop->index}}][close_next_day]">{{$langs['next_day']}}</label>
                            </div>
                        </div>

                        <div class="form-group ml-1">
                            <a href="#" class="js_special_open_close_remove text-danger"
                               title="{{$langs['remove']}}"
                               data-remove_ask="{{$langs['remove_ask']}}">X</a>
                        </div>
                    </div>
                @endforeach
                {{-- END OBEJECT SPECIAL DATES --}}
            @endif
        @endif
    </div>
    {{-- END SPECIAL DATES --}}
</fieldset>
