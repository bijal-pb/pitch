<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Pledge;
use App\Models\Video;
use App\Models\Industry;
use DB;


class AdminController extends Controller
{
    public function index()
    {
        
        $data = (object) [];
       // $data->total_users = User::count();
        $data->total_business_user = User::where('type',2)->count();
        $data->total_pladge_user = User::where('type',1)->count();
        $pledged = Pledge::select(DB::raw("SUM(pledges.amount) as  pledging_amount"))->where('status',2)->first();
        $data->total_pledged = isset($pledged->pledging_amount)? $pledged->pledging_amount : 0;
        $refund = Pledge::select(DB::raw("SUM(pledges.amount) as  refund_amount"))->where('status',4)->first();
        $data->total_refund = isset($refund->refund_amount) ? $refund->refund_amount : 0;
        // $data->total_food_category = FoodCategory::count();

        $chart_data = User::select(\DB::raw("COUNT(*) as count"), \DB::raw("(DATE_FORMAT(created_at, '%d-%m-%Y')) as udate"))
                        ->groupBy('udate')
                        ->get();
        $cData = [];

        foreach($chart_data as $row) {
            $timestamp = null;
            // $date = "1-".$row->monthyear;
            $timestamp = strtotime(date($row->udate)) * 1000; 
            array_push($cData,[$timestamp, (int) $row->count]);
        }

        $data->chart_data = json_encode($cData);
        return view('admin.home')->with("data", $data);
    }
}
