<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Video;
use App\Models\TeamUser;
use App\Models\Setting;
use App\Models\Pledge;
use App\Models\Refund;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Session;
use Hash;
use Mail;

class RefundController extends Controller
{
    public function index(Request $request)
    {
        $refund = Refund::query();
    
        if($request->search != null)
        {
            $refund = $refund->where('first_name','LIKE','%'.$request->search.'%')
                            ->orWhere('last_name','LIKE','%'.$request->search.'%')
                            ->orWhere('email','LIKE','%'.$request->search.'%');
        }
        
        if($request->sortby!= null && $request->sorttype)
        {
            $refund = $refund->orderBy($request->sortby,$request->sorttype);
        }else{
            $refund = $refund->orderBy('id','desc');
        }
        if($request->perPage != null){
            $refund = $refund->paginate($request->perPage);
        }else{
            $refund = $refund->paginate(10);
        }
       
        if($request->ajax())
        {
            return response()->json( view('admin.refund.video_data', compact('u_videos'))->render());
        }
        return view('admin.refund.list' , compact(['refund']));
    }

    public function refunds(Request $request)
    {
        $columns = array( 
            0 =>'id',
            1 =>'from_user',
            2 =>'business_name',
            3 =>'refund_amount',
            4 =>'status',
            5 =>'updated_at',
           
        );  
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $refund = Refund::select('refunds.*','from.name as from_user','to.startup_name as business_name','pledges.amount as refund_amount')
                         ->leftjoin('pledges','refunds.pledge_id','pledges.id')
                         ->leftjoin('users as from','pledges.from_user','from.id')
                         ->leftjoin('users as to','pledges.to_user','to.id');
                       
        if($request->search['value'] != null){
            $refund = $refund->where('from.name','LIKE','%'.$request->search['value'].'%')
                            ->orWhere('to.name','LIKE','%'.$request->search['value'].'%')
                            ->orWhere('to.startup_name','LIKE','%'.$request->search['value'].'%');
                                 
        }
        if($request->length != '-1')
        {
            $refund = $refund->take($request->length);
        }else{
            $refund = $refund->take(Refund::count());
        }
        $refund = $refund->skip($request->start)
                        ->orderBy($order,$dir)
                        ->get();
       
        $data = array();
        if(!empty($refund))
        {
            foreach ($refund as $u_refund)
            {
                $url = route('admin.pledge.get', ['user_id' => $u_refund->id]);
                if($u_refund->status == 1){
                    $status_type = "<span class='badge badge-warning'>Processing</span>";
                }
                if($u_refund->status == 2){
                    $status_type = "<span class='badge badge-success'>Refunded</span>";
                }
                if($u_refund->status == 3){
                    $status_type = "<span class='badge badge-danger'>Declined</span>";
                }
                $user_image =  $u_refund->profile_photo== null ? url('/admin_assets/img/logo.png') : $u_refund->profile_photo;

                $nestedData['id'] = $u_refund->id;
                $nestedData['from_user'] = $u_refund->from_user;
                $nestedData['business_name'] = $u_refund->business_name;
                $nestedData['refund_amount'] = $u_refund->refund_amount;
                $nestedData['status'] = $status_type;
                $nestedData['refund_date'] = $u_refund->updated_at->format('Y-m-d H:i:s');
                $data[] = $nestedData;
            }
        }
        return response()->json([
            'draw' => $request->draw,
            'data' =>$data,
            'recordsTotal' => Refund::count(),
            'recordsFiltered' => $request->search['value'] != null ? $refund->count() : Refund::count(),
        ]);
    }
    public function getRefund(Request $request){
        $u_refund = Refund::find($request->user_id);
        return response()->json(['data'=>$u_refund]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            
		]);

		if($validator->fails())
		{
            return response()->json(['status'=>'error','message' => $validator->errors()->first()]);
        }
    
        if($request->user_id != null)
        {
            $u_refund = Refund::find($request->user_id);
        }else{
            $u_refund = new Refund;
        } 
        $u_refund->reason = $request->reason;
        $u_refund->status = $request->status;
        $u_refund->save();
        return response()->json(['status'=>'success']);
    }
}
