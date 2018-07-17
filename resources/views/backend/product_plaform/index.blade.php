@extends('backend.layout.base')
@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('r_backend/css/libs/nifty-component.css')}}"/>
@endsection
@section('content')
	<div id="content-wrapper">
		<div class="col-lg-12">
			<div class="panel">
				<div class="panel-heading">
					<p class="panel-title">产品分配列表</p>
				</div>
				<div class="panel-body">
					@include('backend.layout.alert_info')
					<table class="table table-responsive table-hover">
						<tr>
							<th>name</th>
							<th>account_uuid</th>
							<th>分配状态</th>
							<th>分配产品数量</th>
							<th>分配详情</th>
						</tr>
						@if(empty($users))
							<tr>
								<td>暂无数据</td>
							</tr>
						@else
							@foreach($users as $value)
							<tr>
								<td>{{$value['name']}}</td>
								<td>{{$value['account_id']}}</td>
								@if(count($value['gerPlafrom'])>0)
									<td>已分配</td>
									<td>{{count($value['gerPlafrom'])}}</td>
									<td><a target="_blank" href="/backend/product/platform/info/{{$value['id']}}">查看详情</a></td>
								@else
									<td>未分配</td>
									<td>0</td>
									<td><a target="_blank" href="/backend/product/platform/info/{{$value['id']}}">去分配</a></td>
								@endif

							</tr>
							@endforeach
						@endif
					</table>
				</div>
			</div>
			{{--分页--}}
			<div style="text-align: center;">
				{{ $users->links() }}
			</div>
		</div>
	</div>
	<div class="md-overlay"></div>
@endsection
@section('foot-js')
	<script charset="utf-8" src="/r_backend/js/modernizr.custom.js"></script>
	<script charset="utf-8" src="/r_backend/js/classie.js"></script>
	<script charset="utf-8" src="/r_backend/js/modalEffects.js"></script>
	<script type="text/javascript">
        $("#close").click(function(){
            $("#modal-1").removeClass('md-show');
            $("#modal-1").addClass('md-hide');
        });
        $(".status_li").click(function(){
            sell_status = $(this).attr('ins_status');
            insurance_id = $(this).attr('insurance_id');
            $("#sell_status").val(sell_status);
            $("#ins_id").val(insurance_id);
        });
	</script>
@stop