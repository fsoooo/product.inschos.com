<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use App\Helper\RsaSignHelp;

class CheckApiSign
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next, $guard = null)
	{
		$all = $request->all();
		if(empty($all['account_id']) || empty($all['timestamp']) || empty($all['biz_content']) || empty($all['sign']))
			return response('缺少必要参数', 401);
		unset($all['sign']);
		krsort($all);
		//account_id验证
		$u = User::where('account_id', $all['account_id'])->first();
		if(empty($u))
			return response('account_id 不存在！', 400);
		$sign_help = new RsaSignHelp();
		$sign = md5($sign_help->base64url_encode(json_encode($all)) . $u->sign_key);
		if($sign !== $request->get('sign')){
			//return response('验签失败', 400);
		}
		return $next($request);
	}

}
