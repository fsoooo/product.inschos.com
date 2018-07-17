<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPlatform extends Model
{
    protected $table = 'product_platform';

	public $timestamps = false;//关闭自动维护

	public static function boot() {
		parent::boot();
		#只添加created_at不添加updated_at
		static::creating(function ($model) {
			$model->created_at = $model->freshTimestamp();
			//$model->updated_at = $model->freshTimeStamp();
		});
	}
    
}
