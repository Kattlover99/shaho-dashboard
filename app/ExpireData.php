<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class ExpireData extends Model
{
    //
    protected $guarded = ['id'];
    protected $table = "expire_data";

	public function get_total_for_report($business_id, $start_date, $end_date) {
		
		$query = self::where('business_id', $business_id);
		
		if (!empty($start_date) && !empty($end_date)) {
            $query->whereBetween(DB::raw('date(date)'), [$start_date, $end_date]);
        }
		
		$query = $query->get();
		
		$total = 0;
		
		foreach ($query as $value) {
			
			$total += ($value->total_1 + $value->total_2) / 2;
		}
		
		return $total;
		
	}
}

