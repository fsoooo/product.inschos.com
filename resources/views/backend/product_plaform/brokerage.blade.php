@extends('backend.layout.base')
@section('content')
    <div id="content-wrapper">
        <div class="col-lg-12">
            <div class="panel">
                <div class="panel-heading">
                    <p class="panel-title">佣金设置<br/>
                        {{$manager['name']??""}}--{{$bind->insurance->display_name??""}}</p>
                </div>
                    <input type="hidden" name="bind_id" value="{{$bind->id}}">
                    <div class="panel-body">
                        @include('backend.layout.alert_info')
                        <table class="table table-responsive table-hover">
                            <tr>
                                <th>缴期方式</th>
                                <th>内部收益佣金比</th>
                                <th>渠道支出佣金比</th>
                                <th>操作</th>
                            </tr>
                            @if(count($bind->insApiBrokerage) < 1)
                                <tr class="ratios">
                                    <td><input name="pay_type[]" type="text"></td>
                                    <td><input name="ratio_for_us[]" type="text"></td>
                                    <td><input name="ratio_for_out[]" type="text"></td>
                                    <td></td>
                                </tr>
                            @else
                                @foreach($bind->insApiBrokerage as $k => $v)
                                    <tr class="ratios">
                                        <td>
                                            @if($v->by_stages_way=='0')
                                                趸交
                                            @else
                                            {{$v->by_stages_way}}
                                                @if($v->pay_type_unit == '年') 年 @elseif($v->pay_type_unit == '月') 月 @elseif($v->pay_type_unit == '天') 天 @endif
                                            @endif
                                        </td>
                                        <td>{{$v->ratio_for_us}}</td>
                                        <td>{{$v->ratio_for_agency}}</td>
                                        @if($v->by_stages_way=='0')
                                            <td></td>
                                        @else
                                            <td><a href="/backend/product/platform/setBrokerageInfo/{{$manager['account_id']}}/{{$bind->insurance->id}}/{{$v->id}}">设置缴期佣金</a></td>
                                        @endif
                                    </tr>
                                @endforeach
                            @endif
                        </table>
                        <div style="text-align: center">
                            <button class="btn btn-primary btn-sm" onclick="window.location.href='/backend/product/platform/info/{{$manager['account_id']}}'">返回分配列表</button>
                        </div>
                    </div>
            </div>
        </div>
    </div>
@endsection
@section('foot-js')
    <script>
        $('.table').on('click', ".delete-list", function(){
            $(this).parent().parent().remove();
        })
    </script>
@stop