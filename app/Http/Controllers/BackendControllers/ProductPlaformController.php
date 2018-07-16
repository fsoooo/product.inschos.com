<?php

namespace App\Http\Controllers\BackendControllers;


use App\Models\Insurance;
use App\Models\User;
use App\Models\ProductPlatform;
use \Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;



class ProductPlaformController extends BaseController
{

	public function index()
	{
		$users = User::with('gerPlafrom')->paginate(config('list_num.user'));
		return view('backend.product_plaform.index',compact('users'));
	}

	public function info($user_id)
	{
		$user_info = User::where('id',$user_id)->select('name','account_id')->first();
		$insurances = Insurance::paginate(config('list_num.user'));
		$product_platfrom = ProductPlatform::where('manager_uuid',$user_info['account_id'])->get();
		return view('backend.product_plaform.info',compact('insurances','user_info','product_platfrom'));
	}

}