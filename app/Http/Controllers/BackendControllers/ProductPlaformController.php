<?php

namespace App\Http\Controllers\BackendControllers;


use App\Models\Insurance;
use App\Models\User;
use App\Models\ProductPlatform;
use App\Models\ProductBrokerage;
use App\Models\InsApiBind;
use App\Models\InsApiBrokerage;

use \Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Helper\TimeStamp;


class ProductPlaformController extends BaseController
{
	public function index()
	{
		$users = User::with(['gerPlafrom' => function ($a) {
			$a->where('end_time', '');
		}])
			->paginate(config('list_num.backend.insurance'));
		return view('backend.product_plaform.index', compact('users'));
	}

	public function info($user_id)
	{
		$user_info = User::where('id', $user_id)->select('name', 'account_id')->first();
		$insurances = Insurance::with([
			'paltfrom' => function ($a) {
				$a->where('end_time', '');
			},
			'brokerage' => function ($a) {
				$a->where('end_time', '0');
			}
		])->paginate(config('list_num.backend.insurance'));
		return view('backend.product_plaform.info', compact('insurances', 'user_info', 'product_platfrom'));
	}

	public function doPlatform()
	{
		$input = $this->request->all();
		if (!isset($input['account_id']) || !isset($input['insurance_ids'])) {
			return json_encode(['status' => 500, 'msg' => '请选择要分配的产品']);
		}
		if (empty($input['account_id']) || empty($input['insurance_ids'])) {
			return json_encode(['status' => 500, 'msg' => '请选择要分配的产品']);
		}
		$repeat_res = ProductPlatform::where('manager_uuid', $input['account_id'])->select('id')->first();
		if (empty($repeat_res)) {
			foreach ($input['insurance_ids'] as $value) {
				if (!empty($value)) {
					ProductPlatform::insert([
						'product_id' => $value,
						'manager_uuid' => $input['account_id'],
						'status' => '1',
						'off_reason' => '',
						'start_time' => TimeStamp::getMillisecond(),
						'end_time' => '',
						'upper_time' => TimeStamp::getMillisecond(),
					]);
				}
			}
		} else {
			$products = ProductPlatform::where('manager_uuid', $input['account_id'])
				->select('product_id')
				->offset(config('list_num.backend.insurance') * ($input['page'] - 1))
				->limit(config('list_num.backend.insurance'))
				->get()
				->toArray();
			$product_ids = [];
			foreach ($products as $product) {
				if (!in_array($product['product_id'], $input['insurance_ids'])) {
					$product_ids[] = $product['product_id'];
				}
			}
			foreach ($input['insurance_ids'] as $value) {
				if (!empty($value)) {
					$product_res = ProductPlatform::where('product_id', $value)
						->where('manager_uuid', $input['account_id'])
						->select('id')
						->first();
					if (empty($product_res)) {
						ProductPlatform::insert([
							'product_id' => $value,
							'manager_uuid' => $input['account_id'],
							'status' => '1',
							'off_reason' => '',
							'start_time' => TimeStamp::getMillisecond(),
							'end_time' => '',
							'upper_time' => TimeStamp::getMillisecond(),
						]);
					} else {
						if (empty($product_ids)) {
							ProductPlatform::where('manager_uuid', $input['account_id'])
								->whereIn('product_id', $products)
								->update([
									'off_reason' => '',
									'end_time' => '',
								]);
						} else {
							ProductPlatform::where('manager_uuid', $input['account_id'])
								->whereIn('product_id', $product_ids)
								->update([
									'off_reason' => '',
									'end_time' => TimeStamp::getMillisecond(),
								]);
						}
					}
				}
			}
		}
		return json_encode(['status' => 200, 'msg' => '产品分配成功']);
	}

	public function setBrokerage($account_id, $product_id)
	{
		$bind = InsApiBind::with(['insurance', 'apiFrom' => function ($q) {
			$q->where('status', 1);
		}, 'insApiBrokerage' => function ($q) {
			$q->where('status', 1);
		}])
			->where('insurance_id', $product_id)
			->first();
		$manager = User::where('account_id', $account_id)->first();
		return view('backend.product_plaform.brokerage', compact('bind', 'manager'));
	}

	public function setBrokerageInfo($account_id, $product_id, $pay_category_id)
	{
		$manager = User::where('account_id', $account_id)->first();
		$InsApiBrokerage = InsApiBrokerage::where('id', $pay_category_id)->first();
		$insurance = Insurance::where('id', $product_id)->first();
		$brokerage = ProductBrokerage::where('manager_uuid', $account_id)
			->where('product_id', $product_id)
			->get();
		return view('backend.product_plaform.brokerage_info', compact('InsApiBrokerage', 'insurance', 'manager', 'brokerage'));
	}

	public function setBrokerageInfoSubmit()
	{
		$input = $this->request->all();
		if (empty($input['product_id']) || empty($input['manager_uuid']) || empty($input['pay_category_id']) || empty($input['by_stages_way'])) {
			return back()->withErrors('参数解析失败')->withInput();
		}
		$repeat = ProductBrokerage::where('product_id', $input['product_id'])
			->where('manager_uuid', $input['manager_uuid'])
			->where('pay_category_id', $input['pay_category_id'])
			->get();
		DB::beginTransaction();
		try {
			if (empty($repeat) || count($repeat) == 0) {
				for ($i = 1; $i <= $input['by_stages_way']; $i++) {
					ProductBrokerage::insert([
						'product_id' => $input['product_id'],
						'manager_uuid' => $input['manager_uuid'],
						'pay_category_id' => $input['pay_category_id'],
						'pay_times_unit' => $input['pay_times_unit'][$i][0],
						'pay_times' => $input['pay_times'][$i][0],
						'ins_brokerage' => $input['ins_brokerage'][$i][0],
						'platform_brokerage' => $input['platform_brokerage'][$i][0],
						'channel_brokerage' => $input['channel_brokerage'][$i][0],
						'agent_brokerage' => $input['agent_brokerage'][$i][0],
						'start_time' => $input['start_time'][$i][0] ?? " ",
						'end_time' => $input['end_time'][$i][0] ?? " ",
					]);
				}
				$product_res = ProductPlatform::where('product_id', $input['product_id'])
					->where('manager_uuid', $input['manager_uuid'])
					->select('id')
					->first();
				if (empty($product_res)) {
					ProductPlatform::insert([
						'product_id' => $input['product_id'],
						'manager_uuid' => $input['manager_uuid'],
						'status' => '1',
						'off_reason' => '',
						'start_time' => TimeStamp::getMillisecond(),
						'end_time' => '',
						'upper_time' => TimeStamp::getMillisecond(),
					]);
				}
			} else {
				for ($i = 1; $i <= $input['by_stages_way']; $i++) {
					ProductBrokerage::where('product_id', $input['product_id'])
						->where('manager_uuid', $input['manager_uuid'])
						->where('pay_category_id', $input['pay_category_id'])
						->where('pay_times', $i)
						->update([
							'ins_brokerage' => $input['ins_brokerage'][$i][0],
							'platform_brokerage' => $input['platform_brokerage'][$i][0],
							'channel_brokerage' => $input['channel_brokerage'][$i][0],
							'agent_brokerage' => $input['agent_brokerage'][$i][0],
							'start_time' => $input['start_time'][$i][0],
							'end_time' => $input['end_time'][$i][0],
						]);
				}
			}
			DB::commit();
			return redirect('/backend/product/platform/setBrokerageInfo/' . $input['manager_uuid'] . '/' . $input['product_id'] . '/' . $input['pay_category_id'])->with('status', '缴期佣金设置成功!');
		} catch (\Exception $e) {
			DB::rollBack();
			return redirect('/backend/product/platform/setBrokerageInfo/' . $input['manager_uuid'] . '/' . $input['product_id'] . '/' . $input['pay_category_id'])->withErrors('缴期佣金设置失败!');
		}
	}
}