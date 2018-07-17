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
		$users = User::with('gerPlafrom')->paginate(config('list_num.user'));
		return view('backend.product_plaform.index', compact('users'));
	}

	public function info($user_id)
	{
		$user_info = User::where('id', $user_id)->select('name', 'account_id')->first();
		$insurances = Insurance::with([
			'paltfrom' => function ($a) {
			$a->where('end_time', '');
		},
			'brokerage'=> function ($a) {
			$a->where('end_time', '0');
		}
		])->paginate(config('list_num.user'));
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
				->get()->toArray();
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
		return json_encode(['status' => 200, 'msg' => '产品分配成功']);
	}

	public function setBrokerage($account_id,$product_id){
		$bind = InsApiBind::with(['insurance', 'apiFrom', 'insApiBrokerage'=>function($q){
			$q->where('status', 1);
		}])
			->where('insurance_id',$product_id)
			->first();
		$manager = User::where('account_id',$account_id)->first();
		$brokerage = ProductBrokerage::where('manager_uuid',$account_id)
			->where('product_id',$product_id)
			->first();
		return view('backend.product_plaform.brokerage', compact('bind','manager','brokerage'));
	}

	public function setBrokeragesubmit(){



	}

}