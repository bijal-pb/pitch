<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiTrait;
use App\Models\User;
use App\Models\Pledge;
use App\Models\Fund;
use App\Models\Follower;
use App\Models\Video;
use App\Models\TeamUser;
use App\Models\Industry;
use App\Models\UserIndustry;
use App\Models\CompanySave;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Auth;
use Hash;
use DB;
use Mail;



class HomeController extends Controller
{
    use ApiTrait;

    /**
     * @OA\Get(
     *     path="/api/business/home",
     *     tags={"Business"},
     *     summary="Business home",
     *     security={{"bearer_token":{}}},
     *     operationId="business-home",
     * 
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity"
     *     ),
     * )
    **/
    public function business_home(Request $request)
    {
        try{
            $home = User::select('id','name','username','email','profile_photo','startup_name','startup_location')
                                ->find(Auth::id());
            $fund = Fund::where('user_id',Auth::id())->first();
            $pledging = Pledge::select(DB::raw("COUNT(pledges.id) as total_pledges"),DB::raw("SUM(pledges.amount) as  pledging_amount"))
                                    ->where('status',2)->where('to_user',Auth::id())->first();

            $day = Pledge::select(DB::raw("SUM(pledges.amount) as  y"),DB::raw("HOUR(updated_at) as x"))
                        ->whereDate('updated_at', Carbon::today())
                        ->where('status',2)
                        ->where('to_user',Auth::id())
                        ->groupBy('x')
                        ->get()->toArray();                       
            $dayData = [];
            for($h=1; $h <= 24; $h++)
            {
                $ind = array_search($h, array_column($day, 'x'));
                // return $index;
                if($ind !== false){
                    $data = ['x' => $h, 'y' => $day[$ind]['y']];
                }else{
                    $data = ['x'=> $h, 'y'=> 0];
                }
                array_push($dayData, $data);
            }

            $week = Pledge::select(DB::raw("SUM(pledges.amount) as  y"),DB::raw("DAYNAME(updated_at) as x"))
                        ->whereBetween('updated_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                        ->whereYear('updated_at', date('Y'))
                        ->where('status',2)
                        ->where('to_user',Auth::id())
                        ->groupBy('x')
                        ->get()->toArray();
            $weeks = ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"];
            $weekData = [];
            $i = 1;
            foreach($weeks as $w)
            {
                $index = array_search(strval($w), array_column($week, 'x'));
                if($index !== false){
                    $data = ['x' => $i, 'y' => $week[$index]['y'], 'label' => $w];
                }else{
                    $data = ['x'=> $i, 'y'=> 0, 'label' => $w];
                }
                array_push($weekData, $data);
                $i++;
            }
            $month = Pledge::select(DB::raw("SUM(pledges.amount) as  y"),DB::raw("DAY(updated_at) as x"))
                        ->whereMonth('updated_at', date('m'))
                        ->whereYear('updated_at', date('Y'))
                        ->where('to_user',Auth::id())
                        ->where('status',2)
                        ->groupBy('x')
                        ->orderBy('x')
                        ->get()->toArray();
            $totalDays = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
            $monthData = [];
            for($d=1; $d <= $totalDays; $d++)
            {
                $index = array_search(strval($d), array_column($month, 'x'));
                if($index !== false){
                    $data = ['x' => $d, 'y' => $month[$index]['y']];
                }else{
                    $data = ['x'=> $d, 'y'=> 0];
                }
                array_push($monthData, $data);
            }
            $year = Pledge::select(DB::raw("SUM(pledges.amount) as y"),DB::raw("MONTHNAME(updated_at) as x"))
                    ->whereYear('updated_at', date('Y'))
                    ->where('status',2)
                    ->where('to_user',Auth::id())
                    ->groupBy('x')
                    ->orderBy('updated_at')
                    ->get()->toArray();
            $months=["January","February","March","April","May","June","July","August","September","October","November","December"];
            $yearData = [];
            $i = 1;
            foreach($months as $m)
            {
                $index = array_search($m, array_column($year, 'x'));
                if($index !== false){
                    $data = ['x' => $i, 'y' => $year[$index]['y'], 'label' => $m];
                }else{
                    $data = ['x'=> $i, 'y'=> 0, 'label' => $m];
                }
                array_push($yearData, $data);
                $i++;
            }
            $five_year = Pledge::select(DB::raw("SUM(pledges.amount) as  y"),DB::raw("YEAR(updated_at) as x"))
                        ->whereYear('updated_at','<=' ,date('Y'))
                        ->whereYear('updated_at','>=' ,date('Y', strtotime('-4 year')))
                        ->where('status',2)
                        ->where('to_user',Auth::id())
                        ->groupBy('x')
                        ->orderBy('updated_at')
                        ->get()->toArray();
            $fiveYearData = [];
            $i = date('Y', strtotime('-4 year'));
            $y = 1;
            for($i; $i <= date('Y'); $i++ )
            {
                $index = array_search(strval($i), array_column($five_year, 'x'));
                if($index !== false){
                    $data = ['x' => $y, 'y' => $five_year[$index]['y'], 'label' => $i];
                }else{
                    $data = ['x'=> $y, 'y'=> 0, 'label' => $i];
                }
                $y++;
                array_push($fiveYearData, $data);
            }

            $home['total_pledging'] = $pledging->pledging_amount + $fund->pleged;
            $home['funding_goal'] = $fund->goal;
            $home['total_pledges'] = $pledging->total_pledges;
            $home['chart'] = [
                "day"=> $dayData,
                "week"=> $weekData,
                "month"=> $monthData,
                "year"=> $yearData,
                "five_year"=> $fiveYearData
            ];

            return $this->response($home,'Business Home!');

        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false,404);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/chart",
     *     tags={"Chart"},
     *     summary="Chart data",
     *     security={{"bearer_token":{}}},
     *     operationId="chart-data",
     * 
     *     @OA\Parameter(
     *         name="business_id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ), 
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity"
     *     ),
     * )
    **/
    public function chart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false,400);
        }
        try{
            $day = Pledge::whereDate('updated_at', Carbon::today())->where('status',2)->where('to_user',$request->business_id)->get(['amount as y','updated_at as x']);                       
            $week = Pledge::select(DB::raw("SUM(pledges.amount) as  y"),DB::raw("DAYNAME(updated_at) as x"))
                        ->whereBetween('updated_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                        ->whereYear('updated_at', date('Y'))
                        ->where('status',2)
                        ->where('to_user',$request->business_id)
                        ->groupBy('x')
                        ->get();
            $month = Pledge::select(DB::raw("SUM(pledges.amount) as  y"),DB::raw("DATE(updated_at) as x"))
                        ->whereMonth('updated_at', date('m'))
                        ->whereYear('updated_at', date('Y'))
                        ->where('status',2)
                        ->where('to_user',$request->business_id)
                        ->groupBy('x')
                        ->orderBy('x')
                        ->get();
            $year = Pledge::select(DB::raw("SUM(pledges.amount) as  y"),DB::raw("MONTHNAME(updated_at) as x"))
                    ->whereYear('updated_at', date('Y'))
                    ->where('status',2)
                    ->where('to_user',$request->business_id)
                    ->groupBy('x')
                    ->orderBy('updated_at')
                    ->get();
            $five_year = Pledge::select(DB::raw("SUM(pledges.amount) as  y"),DB::raw("YEAR(updated_at) as x"))
                        ->whereYear('updated_at','<=' ,date('Y'))
                        ->whereYear('updated_at','>=' ,date('Y', strtotime('-4 year')))
                        ->where('status',2)
                        ->where('to_user',$request->business_id)
                        ->groupBy('x')
                        ->orderBy('updated_at')
                        ->get();
            $home = [
                "day"=> $day,
                "week"=> $week,
                "month"=> $month,
                "year"=> $year,
                "five_year"=> $five_year
            ];
            return $this->response($home,'Chart Data!');
        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false,404);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/user/home",
     *     tags={"Pledge User"},
     *     summary="user home",
     *     security={{"bearer_token":{}}},
     *     operationId="user-home",
     * 
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity"
     *     ),
     * )
    **/
    public function user_home(Request $request)
    {
        try{
            $videos = Video::select('videos.*','users.id as company_id', 'users.startup_name', 'users.username','users.startup_location', 'users.profile_photo', 'users.is_verified')
                      ->leftJoin('users','videos.user_id','users.id')  
                      ->where('videos.type',1)->inRandomOrder()->get();
            return $this->response($videos,'Pledge user Home!');

        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false,404);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/following",
     *     tags={"Follow"},
     *     summary="Follow to business",
     *     security={{"bearer_token":{}}},
     *     operationId="follow",
     * 
     *     @OA\Parameter(
     *         name="follow_to",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),  
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=true,
     *         description="1-follow | 2-unfollow",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ), 
     *     
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity"
     *     ),
     * )
    **/
    public function following(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'follow_to' => 'required',
            'status' => 'required|in:1,2'
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false,400);
        }
        try{
            if($request->status == 1){
                $check_follow = Follower::where('follow_by',Auth::id())->where('follow_to',$request->follow_to)->first();
                if($check_follow){
                    return $this->response([],'Already Followed!');
                }
                $follow = new Follower;
                $follow->follow_by = Auth::id();
                $follow->follow_to = $request->follow_to;
                $follow->save();
                $follow_by = User::find(Auth::id());
                $follow_to = User::find($request->follow_to);
                sendPushNotification($follow_to->device_token,$follow_by->name.' has started following you.',$follow_by->name.' has started following you.',1,$follow_to->id);
                return $this->response([],'Followed Successfully!');
            }else{
                $check_follow = Follower::where('follow_by',Auth::id())->where('follow_to',$request->follow_to)->first();
                if($check_follow){
                    $check_follow->delete();
                    return $this->response([],'Unfollowed Successfully!');
                }
            }
            
        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false,404);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/following/list",
     *     tags={"Follow"},
     *     summary="Following list",
     *     security={{"bearer_token":{}}},
     *     operationId="following-list",
     * 
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),  
     *     
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity"
     *     ),
     * )
    **/
    public function following_list(Request $request)
    {
        try{
            $followings = Follower::select('users.id','users.name','users.username','users.profile_photo','users.startup_name','users.startup_location');
            if(Auth::user()->type == 1){
                $followings = $followings->leftJoin('users','followers.follow_to','users.id')
                ->where('followers.follow_by',Auth::id());
            }else{
                $followings = $followings->leftJoin('users','followers.follow_by','users.id')
                ->where('followers.follow_to',Auth::id());
            }

            if($request->search != null){
                $followings = $followings->where('users.name','LIKE','%'.$request->search.'%')
                                        ->orWhere('users.username','LIKE','%'.$request->search.'%');
            }
            $followings = $followings->orderBy('followers.id','desc')->paginate(50);
            return $this->response($followings,'Following List!');
        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false,404);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/company/profile",
     *     tags={"Business"},
     *     summary="Business Profile",
     *     security={{"bearer_token":{}}},
     *     operationId="business-profile",
     * 
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity"
     *     ),
     * )
    **/
    public function company_profile(Request $request)
    {
        try{
            $home = User::select('id','name','username','email','profile_photo','startup_name','startup_location','bio')
                                ->find(Auth::id());
            $short_video = Video::where('type',1)->where('user_id',Auth::id())->orderBy('id','desc')->get();
            $long_video = Video::where('type',2)->where('user_id',Auth::id())->orderBy('id','desc')->get();
            $fund = Fund::where('user_id',Auth::id())->first();
            $pledging = Pledge::select(DB::raw("COUNT(pledges.id) as total_pledges"),DB::raw("SUM(pledges.amount) as  pledging_amount"))
                                    ->where('to_user',Auth::id())->first();
            $followers = Follower::where('follow_to',Auth::id())->count();
            $team = TeamUser::select('users.id','users.name','users.username','users.email','users.profile_photo')
                            ->leftJoin('users','team_users.user_id','users.id')
                            ->where('business_id',Auth::id())
                            ->get();


            $home['pledged_amount'] = $pledging->pledging_amount + $fund->pleged;
            $home['total_pledges'] = $pledging->total_pledges;
            $home['followers'] = $followers;
            $home['videos'] = $short_video;
            $home['tv'] = $long_video;
            $home['team'] = $team;

            return $this->response($home,'Company Profile!');

        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false,404);
        }
    }

   

    /**
     * @OA\Get(
     *     path="/api/company/detail",
     *     tags={"Company"},
     *     summary="company Detail",
     *     security={{"bearer_token":{}}},
     *     operationId="company-detail",
     * 
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ), 
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity"
     *     ),
     * )
    **/
   public function company_detail(Request $request)
   {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false,400);
        }
       try{
           $home = User::select('id','name','username','email','profile_photo','startup_name','startup_location','bio')
                               ->find($request->user_id);
           $short_video = Video::where('type',1)->where('user_id',$request->user_id)->orderBy('id','desc')->get();
           $long_video = Video::where('type',2)->where('user_id',$request->user_id)->orderBy('id','desc')->get();
           $fund = Fund::where('user_id',$request->user_id)->first();
           $pledging = Pledge::select(DB::raw("COUNT(pledges.id) as total_pledges"),DB::raw("SUM(pledges.amount) as  pledging_amount"))
                                   ->where('to_user',$request->user_id)->first();
           $followers = Follower::where('follow_to',$request->user_id)->count();
           $team = TeamUser::select('users.id','users.name','users.username','users.email','users.profile_photo')
                           ->leftJoin('users','team_users.user_id','users.id')
                           ->where('business_id',$request->user_id)
                           ->get();

           $user_follow = Follower::where('follow_by',Auth::id())->where('follow_to',$request->user_id)->first();
           $company_save = CompanySave::where('user_id',Auth::id())->where('company_id',$request->user_id)->first();
           $home['pledged_amount'] = $pledging->pledging_amount + $fund->pleged;
           $home['total_pledges'] = $pledging->total_pledges;
           $home['followers'] = $followers;
           $home['videos'] = $short_video;
           $home['tv'] = $long_video;
           $home['team'] = $team;
           $home['user_follow'] = isset($user_follow) ? 1 : 2;
           $home['company_save'] = isset($company_save) ? 1 : 2;

           return $this->response($home,'Company Profile!');

       }catch(Exception $e){
           return $this->response([], $e->getMessage(), false,404);
       }
   }

    /**
     * @OA\Get(
     *     path="/api/company/list",
     *     tags={"Company"},
     *     summary="company list",
     *     security={{"bearer_token":{}}},
     *     operationId="company-list",
     * 
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),  
     *     
     *     @OA\Parameter(
     *         name="industry_id",
     *         in="query",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),  
     *     
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity"
     *     ),
     * )
    **/
    public function company_list(Request $request)
    {
        try{
            
            if($request->search == null && $request->industry_id == null){
                $data['Verified Start Ups'] = User::select('users.id','users.name','users.username','users.startup_name','users.startup_location','users.profile_photo','users.is_verified')
                                                    ->where('type',2)->where('is_verified',1)->orderBy('id','desc')->get();
                $industries = Industry::get();
                foreach($industries as $ind){
                    $userCount = UserIndustry::where('industry_id',$ind->id)->count();
                    if($userCount > 0){
                        $userIds = UserIndustry::where('industry_id',$ind->id)->pluck('user_id')->toArray();
                        $data[$ind->name] = User::select('users.id','users.name','users.username','users.startup_name','users.startup_location','users.profile_photo','users.is_verified')
                                                    ->whereIn('users.id',$userIds)
                                                    ->orderBy('users.id','desc')
                                                    ->get();
                    }
                    
                }
            }else{

                $data = User::select('users.id','users.name','users.username','users.startup_name','users.startup_location','users.profile_photo','users.is_verified')
                            ->leftJoin('user_industries','users.id','user_industries.user_id');

                if($request->search != null){
                    $data = $data->where('users.name','LIKE','%'.$request->search.'%')
                                 ->orWhere('users.username','LIKE','%'.$request->search.'%');
                }
                if($request->industry_id != null){
                    $data = $data->where('user_industries.industry_id',$request->industry_id);
                }
                $data = $data->where('users.type',2)
                            ->orderBy('users.id','desc')
                            ->get();
            }

            return $this->response($data,'Companies List!');
        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false,404);
        }

    }

      /**
     * @OA\Post(
     *     path="/api/company/save",
     *     tags={"Company"},
     *     security={{"bearer_token":{}}},  
     *     summary="save company",
     *     operationId="company-save",
     * 
     *    @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ), 
     * 
     *    @OA\Parameter(
     *         name="type",
     *         in="query",
     *         required=true,
     *         description="1 - save | 2 -remove",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ), 
     *       
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity"
     *     ),
     * )
    **/
    public function company_save(Request $request){
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:1,2',
            'company_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false,400);
        }

        try{
            $checkSave = CompanySave::where('user_id',Auth::id())->where('company_id',$request->company_id)->first();
            if($request->type == 1){
                if(!isset($checkSave)){
                    $company = new CompanySave;
                    $company->user_id = Auth::id();
                    $company->company_id = $request->company_id;
                    $company->save();
                    return $this->response($company, 'Company saved successfully!');
                }
                return $this->response('', 'Company already saved!');
            }
            if($request->type == 2){
                if(isset($checkSave)){
                    $checkSave->delete();
                }
                return $this->response('', 'Company removed successfully!');
            }
        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false,404);
        }
    }

     /**
     * @OA\Get(
     *     path="/api/company/save/list",
     *     tags={"Company"},
     *     summary="Save company list",
     *     security={{"bearer_token":{}}},
     *     operationId="company-save-list",
     * 
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity"
     *     ),
     * )
    **/
    public function company_save_list(Request $request)
    {
        try{
            $companyIds = CompanySave::where('user_id',Auth::id())->pluck('company_id')->toArray();
            $company = User::whereIn('id',$companyIds)->get();
          
            return $this->response([
                'companies' => $company,
            ], 'Saved company list!');
        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false,404);
        }
    }

}
