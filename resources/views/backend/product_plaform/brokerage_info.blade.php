@extends('backend.layout.base')
@section('content')
	<div id="content-wrapper">
		<div class="col-lg-12">
			<div class="panel">
				<div class="panel-heading">
					<p class="panel-title">佣金设置-缴期佣金设置<br/>
						{{$manager['name']??""}}--{{$insurance->display_name??""}}
						--{{$InsApiBrokerage->by_stages_way}}{{$InsApiBrokerage->pay_type_unit}}</p>
				</div>
				<form action="{{url('/backend/product/platform/setBrokerageInfoSubmit')}}" method="post">
					{{ csrf_field() }}
					<input type="hidden" name="product_id" value="{{$insurance->id}}">
					<input type="hidden" name="manager_uuid" value="{{$manager['account_id']}}">
					<input type="hidden" name="pay_category_id" value="{{$InsApiBrokerage['id']}}">
					<input type="hidden" name="by_stages_way" value="{{$InsApiBrokerage['by_stages_way']}}">
					<div class="panel-body">
						@include('backend.layout.alert_info')
						<table class="table table-responsive table-hover">
							<tr>
								<th>缴费次数</th>
								<th>单位</th>
								<th>天眼佣金</th>
								<th>平台佣金</th>
								<th>渠道佣金</th>
								<th>代理人佣金</th>
								<th>开始时间</th>
								<th>结束时间</th>
							</tr>
							@if(!empty($InsApiBrokerage->by_stages_way)&&$InsApiBrokerage->by_stages_way!=0)
								@for($i=1;$i<=$InsApiBrokerage->by_stages_way;$i++)
									<tr class="ratios">
										<td><input name="pay_times[{{$i}}][]" value="{{$i}}" type="text"></td>
										<td>
											<select class='unit' name="pay_times_unit[{{$i}}][]">
												<option value="年"
														@if($InsApiBrokerage->pay_type_unit == '年') selected @endif>年
												</option>
												<option value="月"
														@if($InsApiBrokerage->pay_type_unit == '月') selected @endif>月
												</option>
												<option value="天"
														@if($InsApiBrokerage->pay_type_unit == '天') selected @endif>天
												</option>
											</select>
										</td>
										<td><input name='ins_brokerage[{{$i}}][]' type="text" value="{{$brokerage[$i-1]['ins_brokerage']}}"></td>
										<td><input name='platform_brokerage[{{$i}}][]' type="text" value="{{$brokerage[$i-1]['platform_brokerage']}}"></td>
										<td><input name='channel_brokerage[{{$i}}][]' type="text" value="{{$brokerage[$i-1]['channel_brokerage']}}"></td>
										<td><input name='agent_brokerage[{{$i}}][]' type="text" value="{{$brokerage[$i-1]['agent_brokerage']}}"></td>
										<td><input name='start_time[{{$i}}][]' type="text" value="{{$brokerage[$i-1]['start_time']}}"></td>
										<td><input name='end_time[{{$i}}][]' type="text" value="{{$brokerage[$i-1]['end_time']}}"></td>
									</tr>
								@endfor
							@endif
						</table>
						<div style="text-align: center">
							<button class="btn btn-primary btn-sm">提交</button>
							<button type="button" class="btn btn-default btn-sm"
									onclick="window.location.href='/backend/product/platform/setBrokerage/{{$manager['account_id']}}/{{$insurance->id}}'">返回
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection
@section('foot-js')
@stop