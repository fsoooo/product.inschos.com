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
					<button id="submit">确认分配</button>
				</div>
				<div class="panel-body">
					@include('backend.layout.alert_info')
					<table class="table table-responsive table-hover">
						<tr>
							<td><input type="checkbox"  id="checkAllChange"></td>
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
									<td><input type="checkbox" class="insurance_id" name="insurance_ids" value="{{$value['id']}}" @if(!empty($value['paltfrom'])) checked @endif></td>
									<td>{{$value['display_name']}}</td>
									<td>{{$value['name']}}</td>
									<td>
										<a target="_blank" href="/backend/product/platform/setBrokerage/{{$user_info['account_id']}}/{{$value['id']}}">
											@if(empty($value['brokerage'])||count($value['brokerage'])==0) 设置佣金 @else 查看佣金 @endif
										</a>
									</td>
								</tr>
							@endforeach
						@endif
					</table>
				</div>
			</div>
			<div style="text-align: center;">
				{{ $insurances->links() }}
			</div>
		</div>
	</div>
	<button class="md-trigger btn btn-primary mrg-b-lg" data-modal="modal-8" style="display: none" id="notice">消息提醒</button>
	{{--添加--}}
	<div class="md-modal md-effect-8 md-hide" id="modal-8">
		<div class="md-content">
			<div class="modal-header">
				<button class="md-close close">×</button>
				<h3 class="modal-title"><b>提示</b></h3>
			</div>
			<div class="modal-body" id="notice_body"></div>
			<div class="modal-footer">
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
        $("#checkAllChange").click(function() {
            if (this.checked == true) {
                $(".insurance_id").each(function() {
                    this.checked = true;
                });
            } else {
                $(".insurance_id").each(function() {
                    this.checked = false;
                });
            }
        });
        $("#submit").click(function () {
            var arr = new Array();
            var account_id = "{{$user_info['account_id']}}";
            $(".insurance_id").each(function(i) {
                if (this.checked == true) {
                    arr[i] = $(this).val();
                }
            });
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/backend/product/platform/doPlatform",
                type: "post",
                data: {
                    'account_id': account_id,
                    'insurance_ids': arr
                },
                dataType: "json",
                success: function (data) {
					$("#notice_body").html(data.msg);
					$("#notice").click();
                }
            });
		});
	</script>
@stop