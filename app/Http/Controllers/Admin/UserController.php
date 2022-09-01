<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Session;
use Hash;
use Mail;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::where('type',1);
        if($request->search != null)
        {
            $users = $users->where('first_name','LIKE','%'.$request->search.'%')
                            ->orWhere('last_name','LIKE','%'.$request->search.'%')
                            ->orWhere('email','LIKE','%'.$request->search.'%');
        }
        
        if($request->sortby!= null && $request->sorttype)
        {
            $users = $users->orderBy($request->sortby,$request->sorttype);
        }else{
            $users = $users->orderBy('id','desc');
        }
        if($request->perPage != null){
            $users = $users->paginate($request->perPage);
        }else{
            $users = $users->paginate(10);
        }
        if($request->ajax())
        {
            return response()->json( view('admin.users.user_data', compact('users'))->render());
        }
        return view('admin.users.list' , compact(['users']));
    }

    public function users(Request $request)
    {
        $columns = array( 
            0 =>'id', 
            1 =>'profile_photo',
            2 =>'name',
            3 =>'email',
           
        );  
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $users = User::where('type',1);
        if($request->search['value'] != null){
            $users = $users->where('name','LIKE','%'.$request->search['value'].'%')
                            ->orWhere('email','LIKE','%'.$request->search['value'].'%')
                            ->where('type',1);
        }
        if($request->length != '-1')
        {
            $users = $users->take($request->length);
        }else{
            $users = $users->take(User::count());
        }
        $users = $users->skip($request->start)
                        ->orderBy($order,$dir)
                        ->get();
       
        $data = array();
        if(!empty($users))
        {
            foreach ($users as $user)
            {
                $url = route('admin.user.get', ['user_id' => $user->id]);
                $detail_url = route('admin.users.detail', ['user_id' => $user->id]);
                // $statusUrl = route('admin.user.status.change', ['user_id' => $user->id]);
                // $checked = $user->status == 1 ? 'checked' : '';
                $user_image =  $user->profile_photo== null ? url('/admin_assets/img/logo.png') : $user->profile_photo;

                $nestedData['id'] = $user->id;
                $nestedData['profile_photo'] = "<img width='50' height='50' src=' $user_image' class='rounded-circle shadow-2'>";
                $nestedData['name'] = $user->name;
                $nestedData['email'] = $user->email;
                // $nestedData['status'] = "<div class='custom-control custom-switch'>
                //                             <input type='radio' class='custom-control-input active' data-url='$statusUrl' id='active$user->id' name='active$user->id' $checked>
                //                             <label class='custom-control-label' for='active$user->id'></label>
                //                         </div>";
                $nestedData['action'] = "<a  class='btn btn-primary margin-bottom-10'  href=' $detail_url '><i class='fal fa-list'></i></a>";
                $data[] = $nestedData;

            }
        }
        return response()->json([
            'draw' => $request->draw,
            'data' =>$data,
            'recordsTotal' => User::where('type',1)->count(),
            'recordsFiltered' => $request->search['value'] != null ? $users->count() : User::where('type',1)->count(),
        ]);
    }
    public function getUser(Request $request){
        $user = User::find($request->user_id);
        return response()->json(['data'=>$user]);
    }

    public function user_detail(Request $request)
    {
        // dd($request->user_id);
        // $team_user = TeamUser::with('teamUser')->first();
        // $user_video = Video::with('video')->first();
        
        $user = User::with(['industries'])->find($request->user_id);
        return view('admin.users.detail' ,compact('user'));
        
    }
    public function changeStatus(Request $request){
        $user = User::find($request->user_id);
        if($user->status == 1)
        {
            $user->status = 2;
        }else{
            $user->status = 1;
        }
        $user->save();
        return response()->json(['status'=>'success']);
    }
    public function store(Request $request)
    {
        if($request->user_id != null)
        {
            $user = User::find($request->user_id);
        }else{
            $user = new User;
        }
        $user->name = $request->name;
        $user->profile_photo = $request->profile_photo;
        $user->email = $request->email;
        $user->save();
        return response()->json(['status'=>'success']);
    }
    public function profile()
    {
        $user = User::find(Auth::id());   
        return view('admin.users.profile',compact('user'));
    }
    public function update_profile(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email,'.Auth::id(),
            'profile_photo' => 'nullable|image|mimes:png,jpeg,jpg,svg,ico|max:2048'
		]);

		if($validator->fails())
		{
            return response()->json(['status'=>'error','message' => $validator->errors()->first()]);
        }
        try{
            $user = User::find(Auth::id());
            $user->name = $request->name;
            $user->email = $request->email;
            if($request->hasfile('profile_photo')) {
                $file = $request->file('profile_photo');
                $filename = time().$file->getClientOriginalName();
                $file->move(public_path().'/user/', $filename);  
                $user->profile_photo = asset('/user/' . $filename);
            }
            $user->save();
            return response()->json(['status'=>'success','data'=>$user]);
        }catch(Exception $e){
            return response()->json(['status'=>'error','message' => $e->getMessage()]);
        }
        
    }

    public function password()
    {
        return view('admin.users.password');
    }

    public function change_password(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'current' => 'required|max:255',
            'password' => 'required|max:255|min:8',
		]);

		if($validator->fails())
		{
            return response()->json(['status'=>'error','message' => $validator->errors()->first()]);
        }

        try{
            $user = User::find(Auth::id());
            if($user){
                if(Hash::check($request->current,$user->password)){
                    $user->password =  bcrypt($request->password);
                    $user->save();
                    return response()->json(['status'=>'success']);
                }else{
                    return response()->json(['status'=>'error','message' => 'Enter valid current password!']);
                }   
            }
    
        }catch(Exception $e){
            return response()->json(['status'=>'error','message' => $e->getMessage()]);
        }
    }

    public function admin_login(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required|email|max:255',
            'password' => 'required|max:255',
		]);

		if($validator->fails())
		{
            return response()->json(['status'=>'error','message' => $validator->errors()->first()]);
        }
        $user = User::where('email',$request->email)->first();
        if($user)
        {
            if($user->roles[0]->name != 'Admin')
            {
                return response()->json(['status'=>'error','message' => 'Enter valid email or password!']);
            }
            $credentials = $request->only('email', 'password');
            if (Auth::attempt($credentials)) {
                return response()->json(['status'=>'success']);
            }
        }
  
        return response()->json(['status'=>'error','message' => 'Enter valid email or password!']);
    }

    public function app_setting()
    {
        $setting = Setting::latest()->first();
        return view('admin.setting', compact('setting'));
    }

    public function setting_update(Request $request)
    {
        try{
            $setting = Setting::latest()->first();
            $setting->name = $request->name;
            $setting->url = $request->url;
            $setting->push_token = $request->push_token;
            $setting->api_log = $request->api_log;
            $setting->host = $request->host;
            $setting->port = $request->port;
            $setting->email = $request->email;
            $setting->password = $request->password;
            $setting->from_address = $request->from_address;
            $setting->from_name = $request->from_name;
            $setting->encryption = $request->encryption;
            $setting->save();
            return response()->json(['status'=>'success','data'=>$setting]);
        }catch(Exception $e){
            return response()->json(['status'=>'error','message' => $e->getMessage()]);
        }
    }

    public function forgot_password()
    {
        return view('auth.forgot');
    }

    public function password_mail(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required|email|exists:users',
		]);

		if($validator->fails())
		{
            return response()->json(['status'=>'error','message' => $validator->errors()->first()]);
        }

        $user = User::where('email',$request->email)->first();
        if(empty($user))
        {
            return response()->json(['status'=>'error','message' => 'This email not registered']);
        }

        try{
            $newPass = substr(md5(time()), 0, 10);
            $user->password = bcrypt($newPass);
            $user->save();
            $data = [
                'username' => $user->user_name,
                'password' => $newPass
            ];
            $email = $user->email;
            Mail::send('mail.forgot', $data, function($message) use ($email) {
                $message->to($email, 'test')->subject
                   ('Forgot Password');
            });
            return response()->json(['status'=>'success']);
        }catch(Exception $e){
            return response()->json(['status'=>'error','message' => $e->getMessage()]);
        }

    }

}
