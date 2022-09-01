<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Video;
use App\Models\TeamUser;
use App\Models\Setting;
use App\Models\Pledge;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Session;
use Hash;
use Mail;

class PledgeController extends Controller
{
    public function index(Request $request)
    {
        $pledge = Pledge::select('pledges.*','users.name as from_name', 'users.name as to_name')
                        ->leftJoin('users','pledges.from_user','users.id');
    
        if($request->search != null)
        {
            $pledge = $pledge->where('first_name','LIKE','%'.$request->search.'%')
                            ->orWhere('last_name','LIKE','%'.$request->search.'%')
                            ->orWhere('email','LIKE','%'.$request->search.'%');
        }
        
        if($request->sortby!= null && $request->sorttype)
        {
            $pledge = $pledge->orderBy($request->sortby,$request->sorttype);
        }else{
            $pledge = $pledge->orderBy('id','desc');
        }
        if($request->perPage != null){
            $pledge = $pledge->paginate($request->perPage);
        }else{
            $pledge = $pledge->paginate(10);
        }
       
        if($request->ajax())
        {
            return response()->json( view('admin.pledge.video_data', compact('u_videos'))->render());
        }
        return view('admin.pledge.list' , compact(['pledge']));
    }

    public function pledges(Request $request)
    {
        $columns = array( 
            0 =>'id',
            1 =>'profile_photo',
            2 =>'from_user', 
            3 =>'amount',
            4 =>'status',
            5 =>'updated_at',
        );  
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $pledge = Pledge::select('pledges.*','u1.name as from_username', 'u2.startup_name as to_username')
                                 ->join('users as u1', 'pledges.from_user', '=', 'u1.id')
                                 ->join('users as u2', 'pledges.to_user', '=', 'u2.id');
        

        if($request->search['value'] != null){
            $pledge = $pledge->where('u1.name','LIKE','%'.$request->search['value'].'%')
                            ->orWhere('u2.name','LIKE','%'.$request->search['value'].'%');
                                 
        }
        if($request->length != '-1')
        {
            $pledge = $pledge->take($request->length);
        }else{
            if(isset($request->business_user)){
                $pledge = $pledge->take(Pledge::where('to_user',$request->business_user)->where('status',$request->status)->count());
            }
            if(isset($request->pledge_user)){
                $pledge = $pledge->take(Pledge::where('from_user',$request->pledge_user)->count());
            }
        }
        $pledgeCount = 0;
        if(isset($request->business_user)){
            $pledge = $pledge->where('pledges.to_user',$request->business_user)
                            ->where('pledges.status',$request->status);
            $pledgeCount = Pledge::where('to_user',$request->business_user)->where('status',$request->status)->count();
        }
        if(isset($request->pledge_user)){
            $pledge = $pledge->where('pledges.from_user',$request->pledge_user);
            $pledgeCount = Pledge::where('from_user',$request->pledge_user)->count();
        }

                            
        $pledge = $pledge->skip($request->start)
                            ->orderBy($order,$dir)
                            ->get();
       
        $data = array();
        if(!empty($pledge))
        {
            foreach ($pledge as $u_pledge)
            {
                $url = route('admin.pledge.get', ['user_id' => $u_pledge->id]);
                $user_image =  $u_pledge->profile_photo== null ? url('/admin_assets/img/logo.png') : $u_pledge->profile_photo;

            
                $nestedData['id'] = $u_pledge->id;
                $nestedData['profile_photo'] = "<img src=' $user_image' width='100'>";
                $nestedData['from_username'] = $u_pledge->from_username;
                $nestedData['to_username'] = $u_pledge->to_username;
                $nestedData['amount'] = $u_pledge->amount;
                $nestedData['status_type'] = $u_pledge->status_type;
                $nestedData['updated_at'] = $u_pledge->updated_at;
                $data[] = $nestedData;

            }
        }

        return response()->json([
            'draw' => $request->draw,
            'data' =>$data,
            'recordsTotal' => $pledgeCount,
            'recordsFiltered' => $request->search['value'] != null ? $pledge->count() : $pledgeCount,
        ]);
    }
    public function getPledge(Request $request){
        $u_pledge = Pledge::find($request->user_id);
        return response()->json(['data'=>$u_pledge]);
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
            $u_pledge = Pledge::find($request->user_id);
        }else{
            $u_pledge = new Pledge;
        }
       
        $u_pledge->from_user = $request->from_user;
        $u_pledge->amount = $request->amount;
        $u_pledge->status = $request->status;
        $u_pledge->save();
        return response()->json(['status'=>'success']);
    }
}
