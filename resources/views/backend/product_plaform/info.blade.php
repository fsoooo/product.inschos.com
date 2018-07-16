@extends('backend.layout.base')
@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('r_backend/css/libs/nifty-component.css')}}"/>
@endsection
@section('content')
	<div id="content-wrapper">
		<div class="col-lg-12">
			<div class="panel">
				<div class="panel-heading">
					<p class="panel-title">产品分配详情-{{$user_info['name']??""}}</p>
				</div>
				<div class="panel-heading">
					<button>确认分配</button>
				</div>
				<div class="panel-body">
					@include('backend.layout.alert_info')
					<table class="table table-responsive table-hover">
						<tr>
							<td><input type="checkbox"></td>
							<th ><span>保险产品简称</span></th>
							<th ><span>保险产品全称</span></th>
							<th >操作</th>
						</tr>
						@if(empty($insurances))
							<tr>
								<td>暂无数据</td>
							</tr>
						@else
							@foreach($insurances as $value)
								<tr>
									<td><input type="checkbox" name="insurance_ids[]" value="{{$value['id']}}"></td>
									<td>{{$value['display_name']}}</td>
									<td>{{$value['name']}}</td>
									<td><a target="_blank" href="/backend/product/info">查看详情</a></td>
								</tr>
							@endforeach
						@endif
					</table>
				</div>
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