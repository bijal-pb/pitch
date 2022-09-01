<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Pledge;
use App\Models\Fund;
use App\Models\Follower;
use App\Models\Refund;
use App\Models\Transaction;
use App\Traits\ApiTrait;
use Exception;
use Illuminate\Support\Facades\Validator;
use Auth;
use Hash;
use DB;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Customer;
use Stripe\Refund as Refunds;
use Illuminate\Support\Carbon;
use App\Jobs\Email\EmailJob;

class PledgeController extends Controller
{
    use ApiTrait;
    /**
     * @OA\Get(
     *     path="/api/pledge/list",
     *     tags={"Pledge"},
     *     summary="Business pledge users list with search username and name",
     *     security={{"bearer_token":{}}},
     *     operationId="pledge-list",
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

    public function pledge_list(Request $request)
    {
        try{
            $pledges = Pledge::select('pledges.*','users.name','users.username','users.profile_photo',DB::raw("COUNT('pledges.id') as pledge_count"))
                                ->leftJoin('users','pledges.from_user','users.id')
                                ->where('pledges.to_user',Auth::id())->where('pledges.status',2);
            if($request->search != null){
                $pledges = $pledges->where('users.name','LIKE','%'.$request->search.'%')
                                   ->orWhere('users.username','LIKE','%'.$request->search.'%');
            }
            $pledges =  $pledges->groupBy('users.username')->orderBy('pledges.id','desc')->paginate(50);
            return $this->response($pledges,'Pledges List!');
        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false,404);
        }
                
    }

    /**
     * @OA\Get(
     *     path="/api/pledge/user/detail",
     *     tags={"Pledge"},
     *     summary="Pledge user detail for particular business",
     *     security={{"bearer_token":{}}},
     *     operationId="pledge-user-detail",
     * 
     *     @OA\Parameter(
     *         name="from_user",
     *         in="query",
     *          required=true,
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
    public function pledge_user_detail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_user' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false,400);
        }
        try{
            $pledge_user = User::select('id','name','username','email','profile_photo','firebase_id')
                                ->find($request->from_user);
            $pledging = Pledge::select(DB::raw("COUNT(pledges.id) as total_pledges"),DB::raw("SUM(pledges.amount) as  pledging_amount"))
                                    ->where('from_user',$request->from_user)->where('to_user',Auth::id())->first();
            $following_count = Follower::where('follow_by',$request->from_user)->count();
            $pledge_user['total_pledging'] = $pledging->total_pledges;
            $pledge_user['pledging_amount'] = $pledging->pledging_amount;
            $pledge_user['followings'] =  $following_count;

            return $this->response($pledge_user,'Pledges User Detail!');

        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false,404);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/pledge/history",
     *     tags={"Pledge"},
     *     summary="Business pledge users history",
     *     security={{"bearer_token":{}}},
     *     operationId="pledge-history",
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

    public function pledge_history(Request $request)
    {
        try{
            $pledge_history = Pledge::select('pledges.*','users.name','users.username','users.profile_photo')
                                ->leftJoin('users','pledges.from_user','users.id')
                                ->where('pledges.to_user',Auth::id())->where('pledges.status',2)
                                ->orderBy('pledges.id','desc')->paginate(50);
            
            $fund = Fund::where('user_id',Auth::id())->first();
            $pledging = Pledge::select(DB::raw("COUNT(pledges.id) as total_pledges"),DB::raw("SUM(pledges.amount) as  pledging_amount"))
                                    ->where('to_user',Auth::id())->first();

            return $this->response([
                "pledge_history" => $pledge_history,
                "total_pledging" => $pledging->pledging_amount + $fund->pleged,
                "funding_goals" => $fund->goal,
                "total_pledges" => $pledging->total_pledges
            ],'Pledging History!');
        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false,404);
        }
                
    }

    /**
     * @OA\Post(
     *     path="/api/pledge/add",
     *     tags={"Pledge"},
     *     security={{"bearer_token":{}}},  
     *     summary="Add new pledge",
     *     operationId="pledge-add",
     * 
     *     @OA\Parameter(
     *         name="to_user",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),  
     *     @OA\Parameter(
     *         name="amount",
     *         in="query",
     *         required=true,
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
    public function pledge_add(Request $request){
        $validator = Validator::make($request->all(), [
            'to_user' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false,400);
        }

        try{
            $pledge = new Pledge;
            $pledge->from_user = Auth::id();
            $pledge->to_user = $request->to_user;
            $pledge->amount = $request->amount;
            $pledge->status = 1;
            $pledge->save();
            return $this->response($pledge,'Pledges User Detail!');
        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false,404);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/pledge/transaction",
     *     tags={"Pledge"},
     *     security={{"bearer_token":{}}},  
     *     summary="pledge transaction",
     *     operationId="pledge-transaction",
     * 
     *     @OA\Parameter(
     *         name="pledge_id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="transaction_id",
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
     *         description="1- success | 2 - declined",
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
    public function pledge_transaction(Request $request){
        $validator = Validator::make($request->all(), [
            'pledge_id' => 'required',
            'status' => 'required',
            'transaction_id' =>'required',
        ]);
        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false,400);
        }
        DB::beginTransaction();
        try{
            $transaction = new Transaction;
            $transaction->pledge_id = $request->pledge_id;
            $transaction->transaction_id = $request->transaction_id;
            $transaction->status = $request->status;
            $transaction->save();

            $pledge = Pledge::find($request->pledge_id);
            if($request->status == 1){
                $pledge->status = 2;
                $pledge->save();
                $fund = Fund::where('user_id',$pledge->to_user)->first();
                $fund->pledge_amount = $fund->pledge_amount + $pledge->amount;
                $fund->save();
                $businessUser = User::find($pledge->to_user);
                $pldegeUser = User::find($pledge->from_user);
                sendPushNotification($businessUser->device_token,$pldegeUser->name.' has credited '. $pledge->amount.'$ to fund.',$pldegeUser->name.' has credited '. $pledge->amount.'$ to fund.',1,$businessUser->id);
                $user = $pldegeUser;
                EmailJob::dispatch($user, "App\Mail\InvoiceMail", ["user" => $user, "pledge" => $pledge, "business" => $businessUser, "transaction" => $transaction]);
            }else{
                $pledge->status = 3;
                $pledge->save();
            }
            DB::commit();
            return $this->response($pledge,'Pledge update successfully!');
        }catch(Exception $e){
            DB::rollback();
            return $this->response([], $e->getMessage(), false,404);
        }

    }

    /**
     * @OA\Post(
     *     path="/api/pledge/refund",
     *     tags={"Pledge"},
     *     security={{"bearer_token":{}}},  
     *     summary="pledge refund request",
     *     operationId="pledge-refund",
     * 
     *     @OA\Parameter(
     *         name="pledge_id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),  
     *    @OA\Parameter(
     *         name="reason",
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
    public function pledge_refund(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pledge_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false,400);
        }

        try{
            
            $pledge = Pledge::where('status',2)->find($request->pledge_id);
            if(isset($pledge)){

                $from = Carbon::createFromFormat('Y-m-d H:s:i', $pledge->updated_at);
                $to = Carbon::createFromFormat('Y-m-d H:s:i', now());

                $diff_in_hours = $to->diffInHours($from);

                if($diff_in_hours <= 48){
                    $refund = new Refund;
                    $refund->pledge_id = $request->pledge_id;
                    $refund->reason = $request->reason;
                    $refund->status = 1;
                    $refund->save();
                    
                    $trans = Transaction::where('pledge_id',$pledge->id)->where('status',1)->first();
                    Stripe::setApiKey(env('STRIPE_SECRET'));
                    $refund_pay  = Refunds::create([
                        'payment_intent' => $trans->transaction_id,
                      ]);
                    
                    if($refund_pay->status == "succeeded"){
                        $trans = new Transaction;
                        $trans->pledge_id = $pledge->id;
                        $trans->transaction_id = $refund_pay->id;
                        $trans->status = 3;
                        $trans->save();

                        $pledge->status = 4;
                        $pledge->save();

                        $refund->status = 2;
                        $refund->save();

                        $fund = Fund::where('user_id',$pledge->to_user)->first();
                        $fund->pledge_amount = $fund->pledge_amount - $pledge->amount;
                        $fund->save();

                        return $this->response([],'Refund successfully!');
                    }else{
                        $refund->status = 3;
                        return $this->response([],'Refund request declined, Please try again after some time!');
                    }
                }

                return $this->response([],'Refund will be initiated before 48 hours');
            }
            return $this->response([], 'Enter valid pledge id!', false,404);
            
        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false,404);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/pledge/business/detail",
     *     tags={"Pledge"},
     *     summary="Pledge business detail for particular pledge user",
     *     security={{"bearer_token":{}}},
     *     operationId="pledge-business-detail",
     * 
     *     @OA\Parameter(
     *         name="to_user",
     *         in="query",
     *          required=true,
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
   public function pledge_business_detail(Request $request)
   {
       $validator = Validator::make($request->all(), [
           'to_user' => 'required',
       ]);

       if ($validator->fails()) {
           return $this->response([], $validator->errors()->first(), false,400);
       }
       try{
           $business_user = User::select('id','name','username','startup_name','startup_location','email','profile_photo')
                               ->find($request->to_user);
           $pledges = Pledge::where('from_user', Auth::id())->where('status',2)->where('to_user',$request->to_user)->get();
           $business_user['pledges'] = $pledges;

           $day = Pledge::select(DB::raw("SUM(pledges.amount) as  y"),DB::raw("HOUR(updated_at) as x"))
                        ->whereDate('updated_at', Carbon::today())
                        ->where('status',2)
                        ->where('to_user',$request->to_user)
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
                        ->where('to_user',$request->to_user)
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
                        ->where('to_user',$request->to_user)
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
                    ->where('to_user',$request->to_user)
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
                        ->where('to_user',$request->to_user)
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

            $business_user['pledges'] = $pledges;
            $business_user['chart'] = [
                "day"=> $dayData,
                "week"=> $weekData,
                "month"=> $monthData,
                "year"=> $yearData,
                "five_year"=> $fiveYearData
            ];

           return $this->response($business_user,'Pledges Detail!');

       }catch(Exception $e){
           return $this->response([], $e->getMessage(), false,404);
       }
   }

}
