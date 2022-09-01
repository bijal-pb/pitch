<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Video;
use App\Models\TeamUser;
use App\Models\Setting;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Session;
use Hash;
use Mail;

class BusinessController extends Controller
{
    public function index(Request $request)
    {
       // $users = User::query();
        $business_user = User::where('type',2);
        if($request->search != null)
        {
            $business_user = $business_user->where('users.startup_name','LIKE','%'.$request->search.'%')
                           ->orWhere('users.email','LIKE','%'.$request->search.'%');
        }
        
        if($request->sortby!= null && $request->sorttype)
        {
            $business_user = $business_user->orderBy($request->sortby,$request->sorttype);
        }else{
            $business_user = $business_user->orderBy('id','desc');
        }
        if($request->perPage != null){
            $business_user = $business_user->paginate($request->perPage);
        }else{
            $business_user = $business_user->paginate(10);
        }
        if($request->ajax())
        {
            return response()->json( view('admin.business.detail', compact('business_user'))->render());
        }
        return view('admin.business.list' , compact(['business_user']));
    }

    public function businesses(Request $request)
    {
        $columns = array( 
            0 =>'id', 
            1 =>'profile_photo',
            2 =>'startup_name',
            3 =>'email',
           
        );  
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        //$users = User::query();
       
        $business_user = User::where('type',2);
        if($request->is_verified != null){
            $business_user = $business_user->where('is_verified',$request->is_verified);
        }
        if($request->search['value'] != null){
            $business_user = $business_user->where('startup_name','LIKE','%'.$request->search['value'].'%')
                            ->orWhere('email','LIKE','%'.$request->search['value'].'%')
                            ->where('type',2);
        }
        if($request->length != '-1')
        {
            $business_user = $business_user->take($request->length);
        }else{
            $business_user = $business_user->take();
        }
        $business_user = $business_user->skip($request->start)
                        ->orderBy($order,$dir)
                        ->get();
       
        $data = array();
        if(!empty($business_user))
        {
            foreach ($business_user as $b_user)
            {
                $url = route('admin.business.get', ['user_id' => $b_user->id]);
                $detail_url = route('admin.business.detail', ['user_id' => $b_user->id]);
                // $statusUrl = route('admin.business.status.change', ['user_id' => $b_user->id]);
                // $checked = $b_user->is_verified == 1 ? 'checked' : '';
                $user_image =  $b_user->profile_photo== null ? url('/admin_assets/img/logo.png') : $b_user->profile_photo;
                    
                $nestedData['id'] = $b_user->id;
                $nestedData['profile_photo'] = "<img width='50' height='50' src=' $user_image' class='rounded-circle shadow-2' alt='Image'>";
                $nestedData['startup_name'] = $b_user->startup_name;
                $nestedData['email'] = $b_user->email;
                $nestedData['is_verified'] = $b_user->is_verified == 1 ?  "<span class='badge badge-primary'> Verified</span>" : "<span class='badge badge-danger'>Unverified</span>";
                $nestedData['action'] = "<a  class='btn btn-primary margin-bottom-10'  href=' $detail_url '><i class='fal fa-list'></i></a>";
                $data[] = $nestedData;

            }
        }
        return response()->json([
            'draw' => $request->draw,
            'data' =>$data,
            'recordsTotal' => User::where('type',2)->count(),
            'recordsFiltered' => $request->search['value'] != null ? $business_user->count() : User::where('type',2)->count(),
        ]);
    }

    public function business_detail(Request $request)
    {
        // dd($request->user_id);
        // $team_user = TeamUser::with('teamUser')->first();
        // $user_video = Video::with('video')->first();
        
        $business_user = User::with(['all_uploaded_videos','team_user','fund'])->find($request->user_id);
        $long = Video::where('user_id',$request->user_id)->where('type',2)->count();
        $short = Video::where('user_id',$request->user_id)->where('type',1)->count();
        return view('admin.business.detail' ,compact('business_user','long','short'));
        
    }

    public function changeStatusVerified(Request $request){
        $business_user = User::find($request->user_id);
        $business_user->is_verified = 1;
        $business_user->save();
        return response()->json(['status'=>'success']);
    }

    public function changeStatusUnverified(Request $request){
        $business_user = User::find($request->user_id);
        $business_user->is_verified = 2;
        $business_user->save();
        return response()->json(['status'=>'success']);
    }

    public function getBusiness(Request $request){
        $b_user = User::find($request->user_id);
        return response()->json(['data'=>$b_user]);
    }

   

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'startup_name' => 'nullable|max:255',
            'email' => 'nullable|email',
            'profile_photo' => 'nullable|image|mimes:png,jpeg,jpg,svg,ico|max:2048'
		]);

		if($validator->fails())
		{
            return response()->json(['status'=>'error','message' => $validator->errors()->first()]);
        }

        
        if($request->cat_id != null)
        {
            $business_user = User::find($request->cat_id);
        }else{
            $business_user = new User;
        }
        try{
            $business_user->startup_name = $request->startup_name;
            $business_user->email = $request->email;
            $business_user->type = 2;
            $business_user->profile_photo = $request->profile_photo;
            $business_user->is_verified = $request->is_verified;
            $business_user->save();
            $business_user->assignRole(['Business']);
            return response()->json(['status'=>'success']);
        }catch(Exception $e){
            return response()->json(['status'=>'error','message' => $e->getMessage()]);
        }
        
    }
}

