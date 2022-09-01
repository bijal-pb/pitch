<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pledge;
use App\Models\Fund;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PDF;
use DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $users = User::select('id', 'name', 'startup_name')->where('type', 2)->get();
        $users_p = User::select('id', 'name')->where('type', 1)->get();
        
        return view('admin.report.list', compact(['users', 'users_p']));
    }

    public function getReport(Request $request)
    {
        $business = User::select('id','name','startup_name','startup_location','profile_photo')->find($request->business_id);
        $fund = Fund::where('user_id',$request->business_id)->first();
        $pledges = Pledge::with('pledge_by')
                        ->where('to_user',$request->business_id)
                        ->get();
        $total = Pledge::select(DB::raw("COUNT(pledges.id) as total_pledges"),DB::raw("SUM(pledges.amount) as  pledging_amount"))
                        ->where('to_user',$request->business_id)->first();
        return response()->json([
            'status' => 'success',
            'business' => $business,
            'pledges' => $pledges,
            'total' => $total,
            'goal' => $fund->rise,
        ]);
    }

    public function generateReport(Request $request)
    {
        $business = User::select('id','name','startup_name','startup_location','profile_photo','website','email')->find($request->business_id);
        $fund = Fund::where('user_id',$request->business_id)->first();
        $pledges = Pledge::with('pledge_by')
                        ->where('to_user',$request->business_id)
                        ->get();
        
        $totalPledge = Pledge::select(DB::raw("SUM(pledges.amount) as  pledging_amount"))
                                    ->where('status',2)->where('to_user',$request->business_id)->first();
        $totalRefund = Pledge::select(DB::raw("SUM(pledges.amount) as  pledging_amount"))
                                    ->where('status',4)->where('to_user',$request->business_id)->first();
        $data = [
            'startup_name' => $business->startup_name,
            'startup_location' => $business->startup_location,
            'website' => $business->website,
            'email' => $business->email,
            'logo' => $business->profile_photo,
            'pledges' => $pledges,
            'fund' => $fund,
            'total_refund' => $totalRefund->pledging_amount,
            'total_pledge' => $totalPledge->pledging_amount
        ];
        $pdf = PDF::loadView('pdf.report', $data);

        $fileName =  'business'.$request->business_id.time().'.'. 'pdf' ;

        $path = public_path('pdf/');
        $pdf->save($path . '/' . $fileName);
        $pdf = public_path('pdf/'.$fileName);
        return response()->download($pdf);
    }

}
