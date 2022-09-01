<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Jobs\Email\EmailJob;
use App\Models\Bring;
use App\Models\Eligible;
use App\Models\Fund;
use App\Models\Industry;
use App\Models\TeamRequest;
use App\Models\TeamUser;
use App\Models\User;
use App\Models\UserBring;
use App\Models\UserDocument;
use App\Models\UserEligible;
use App\Models\UserIndustry;
use App\Models\UserOtp;
use App\Models\Video;
use App\Traits\ApiTrait;
use Auth;
use DB;
use Exception;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Mail;
use Stripe\Account;
use Stripe\AccountLink;
use Stripe\Customer;
use Stripe\Stripe;
use Stripe\Token;

/**
 * @OA\Info(
 *      description="",
 *     version="1.0.0",
 *      title="Pitch",
 * )
 **/

/**
 *  @OA\SecurityScheme(
 *     securityScheme="bearer_token",
 *         type="http",
 *         scheme="bearer",
 *     ),
 **/
class UserController extends Controller
{

    use ApiTrait;

    /**
     *  @OA\Post(
     *     path="/api/register",
     *     tags={"User"},
     *     summary="Register",
     *     operationId="register",
     *
     *   @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *   @OA\Parameter(
     *         name="username",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *    @OA\Parameter(
     *         name="age",
     *         in="query",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *
     *   @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="1-Pledge User | 2- Business user",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="website",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="description",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *   @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="1-Pledge User | 2- Business user",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *   @OA\Parameter(
     *         name="description",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *   @OA\Parameter(
     *         name="profile_photo",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *   @OA\Parameter(
     *         name="startup_video",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *   @OA\Parameter(
     *         name="video_length",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *   @OA\Parameter(
     *         name="thumbnail",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *
     *   @OA\Parameter(
     *         name="startup_name",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *   @OA\Parameter(
     *         name="startup_location",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *   @OA\Parameter(
     *         name="goal",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *   @OA\Parameter(
     *         name="pleged",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *   @OA\Parameter(
     *         name="rise",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *   @OA\Parameter(
     *         name="brings[]",
     *         in="query",
     *         description="value pass like 1,2",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *   @OA\Parameter(
     *         name="eligibles[]",
     *         in="query",
     *         description="value pass like 1,2",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *   @OA\Parameter(
     *         name="industries[]",
     *         in="query",
     *         description="value pass like 1,2",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *   @OA\Parameter(
     *         name="documents[]",
     *         in="query",
     *         description="{ 'type': 1, 'document': url }  note: 1 - Government | 2 - address | 3 - startup",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *   @OA\Parameter(
     *         name="teams[]",
     *         description="{ 'user_id': (if got then pass otherwise pass null), 'first_name': 'xyz', 'last_name':'a', 'email':'xyz@gmail.com', 'position': 'test'}",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *   @OA\Parameter(
     *         name="key",
     *         description="if team user register by deep link then pass",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *    @OA\Parameter(
     *         name="firebase_id",
     *         in="query",
     *         required=true,
     *         description="Firebase id for chat",
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
     * )
     **/

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'username' => 'required|unique:users',
            'password' => 'required|min:8',
            'type' => 'required|in:1,2',
            'firebase_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        DB::beginTransaction();
        try {
            $user = new User();
            $user->name = $request->name;
            $user->age = $request->age;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->type = $request->type;
            $user->username = $request->username;
            $user->firebase_id = $request->firebase_id;

            // business user for require field entry
            if ($request->type == 2) {
                $user->website = $request->website;
                $user->description = $request->description;
                $user->is_verified = 2;
                $user->profile_photo = $request->profile_photo;
                $user->startup_name = $request->startup_name;
                $user->startup_location = $request->startup_location;
                $user->bio = $request->bio;
            }
            $user->save();

            if (isset($request->brings) && is_array($request->brings)) {
                foreach ($request->brings as $b) {
                    $bring = new UserBring;
                    $bring->user_id = $user->id;
                    $bring->bring_id = $b;
                    $bring->save();
                }
            }
            if (isset($request->eligibles) && is_array($request->eligibles)) {
                foreach ($request->eligibles as $e) {
                    $eligible = new UserEligible;
                    $eligible->user_id = $user->id;
                    $eligible->eligible_id = $e;
                    $eligible->save();
                }
            }
            if (isset($request->industries) && is_array($request->industries)) {
                foreach ($request->industries as $i) {
                    $industry = new UserIndustry;
                    $industry->user_id = $user->id;
                    $industry->industry_id = $i;
                    $industry->save();
                }
            }
            if (isset($request->documents) && is_array($request->documents)) {
                foreach ($request->documents as $d) {
                    $document = new UserDocument;
                    $document->user_id = $user->id;
                    $document->type = $d['doc_type'];
                    $document->document = $d['document'];
                    $document->save();
                }
            }
            if (isset($request->teams) && is_array($request->teams)) {
                foreach ($request->teams as $t) {
                    $teams = new TeamRequest;
                    $teams->business_id = $user->id;
                    $teams->user_id = isset($t['user_id']) && $t['user_id'] != 'null' ? $t['user_id'] : null;
                    $teams->first_name = $t['first_name'];
                    $teams->last_name = $t['last_name'];
                    $teams->email = $t['email'];
                    $teams->position = $t['position'];
                    // $key = rand(10000, 99999) . time() . rand(10000, 99999);
                    $teams->key = $t['key'];
                    $teams->link = $t['link'];
                    $teams->save();
                }
            }
            if ($request->type == 2) {
                $fund = new Fund;
                $fund->user_id = $user->id;
                $fund->goal = $request->goal;
                $fund->pleged = $request->pleged;
                $fund->rise = $request->rise;
                $fund->save();
            }

            if (isset($request->startup_video) && $request->startup_video != null) {
                $vid = new Video;
                $vid->user_id = $user->id;
                $vid->type = 1;
                $vid->length = $request->video_length;
                $vid->video = $request->startup_video;
                $vid->thumbnail = $request->thumbnail;
                $vid->save();
            }

            if ($request->type == 1) {
                $user->assignRole([2]);
            }
            if ($request->type == 2) {
                $user->assignRole([3]);
            }

            $teamRequests = TeamRequest::where('business_id', $user->id)->get();
            foreach ($teamRequests as $ts) {
                if ($ts->user_id == null) {
                    $user = (object) ['email' => $ts->email, 'first_name' => $ts->first_name, 'last_name' => $ts->last_name, 'position' => $ts->position];
                    // $link = "https://pitchapp.page.link/invite?key" . $ts->key;
                    $link = $ts->link;
                    $businessUser = User::find($ts->business_id);
                    EmailJob::dispatch($user, "App\Mail\TeamRequestMail", ["user" => $user, "business" => $businessUser, "link" => $link]);
                } else {
                    $team = new TeamUser;
                    $team->business_id = $ts->business_id;
                    $team->user_id = $ts->user_id;
                    $team->position = $ts->position;
                    $team->save();
                    $user = User::find($ts->user_id);
                    $businessUser = User::find($ts->business_id);
                    EmailJob::dispatch($user, "App\Mail\WelcomeTeamMail", ["user" => $user, 'business' => $businessUser]);
                    $teamUser = User::find($ts->user_id);
                    sendPushNotification($teamUser->device_token, $businessUser->startup_name . ' has added you to their team.', $businessUser->startup_name . ' has added you to their team.', 1, $teamUser->id);
                }
            }
            if ($request->key != null) {
                $teamRequest = TeamRequest::where('key', $request->key)->first();
                if (isset($teamRequest)) {
                    $team = new TeamUser;
                    $team->business_id = $teamRequest->business_id;
                    $team->user_id = $user->id;
                    $team->position = $teamRequest->position;
                    $team->save();
                    $businessUser = User::find($teamRequest->business_id);
                    $teamUser = User::find($user->id);
                    sendPushNotification($teamUser->device_token, $businessUser->startup_name . ' has added you to their team.', $businessUser->startup_name . ' has added you to their team.', 1, $teamUser->id);
                }else{
                    return $this->response([], 'Enter valid key!', false, 404);
                }
            }
            DB::commit();
            return $this->response('', 'Registered Successully!');
        } catch (Exception $e) {
            DB::rollback();
            return $this->response([], $e->getMessage(), false, 404);
        }
    }

    /**
     *  @OA\Post(
     *     path="/api/login",
     *     tags={"Login"},
     *     summary="Login",
     *     operationId="login",
     *
     *     @OA\Parameter(
     *         name="username",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="firebase_id",
     *         in="query",
     *         description="Firebase id for chat",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="device_type",
     *         in="query",
     *         description="android | ios",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="device_token",
     *         in="query",
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
     * )
     **/

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
            'firebase_id' => 'nullable',
        ],
            [
                'username' => 'Username is required',
                'password' => 'Password is required',
            ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $credentials = request(['username', 'password']);
            if (!Auth::attempt($credentials)) {
                return $this->response([], 'Please enter valid username or password!', false, 401);
            }
            $user = User::where('username', $request->username)->first();
            $user->device_type = $request->device_type;
            $user->device_token = $request->device_token;
            if($request->firebase_id != null){
                $user->firebase_id = $request->firebase_id;
            }
            $user->save();
            $user->tokens()->delete();
            if ($user->status == 2) {
                return $this->response([], 'Your account is blocked, Please contact administrator!', false, 401);
            }
            $token = $user->createToken('API')->accessToken;

            if ($user->stripe_cust_id == null) {
                Stripe::setApiKey(env('STRIPE_SECRET'));
                $stripe = Customer::create([
                    'email' => $user->email,
                    'name' => $user->name,
                    'description' => $user->type == 1 ? 'pldge user' : 'business user',
                ]);
                if ($stripe->id != null) {
                    $user->stripe_cust_id = $stripe->id;
                    $user->save();
                }
            }
            $user['token'] = $token;
            return $this->response($user, 'User login successfully!');
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false, 404);
        }
    }

    /**
     *  @OA\Post(
     *     path="/api/profile/update",
     *     tags={"User"},
     *     summary="Edit Profile",
     *     security={{"bearer_token":{}}},
     *     operationId="edit-profile",
     *
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="age",
     *         in="query",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="startup_name",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="startup_location",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="bio",
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
    public function edit_profile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'name' => 'nullable|max:255',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }

        try {
            $user = User::find(Auth::id());
            if ($user) {
                $user->name = $request->name;
                $user->email = $request->email;
                $user->age = $request->age;
                $user->startup_name = $request->startup_name;
                $user->startup_location = $request->startup_location;
                $user->bio = $request->bio;
                $user->save();
                return $this->response($user, 'Profile updated successfully!');
            }
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false, 404);
        }
    }

    /**
     *  @OA\Post(
     *     path="/api/profile/edit/image",
     *     tags={"User"},
     *     summary="Edit Profile Image",
     *     security={{"bearer_token":{}}},
     *     operationId="edit-profile-image",
     *
     *     @OA\Parameter(
     *         name="profile_photo",
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
    public function edit_profile_image(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            if ($user) {
                $user->profile_photo = $request->profile_photo;
                $user->save();
                return $this->response($user, 'Profile image updated successfully!');
            }
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false, 404);
        }
    }
    /**
     *  @OA\Get(
     *     path="/api/logout",
     *     tags={"User"},
     *     security={{"bearer_token":{}}},
     *     summary="Logout",
     *     security={{"bearer_token":{}}},
     *     operationId="Logout",
     *
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
    public function logout()
    {
        try {
            $user = User::find(Auth::id());
            $user->tokens()->delete();
            $user->device_type = null;
            $user->device_token = null;
            $user->save();
            return $this->response('', 'Logout Successfully!');
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false, 404);
        }
    }
    /**
     *  @OA\Post(
     *     path="/api/change/password",
     *     tags={"User"},
     *     summary="Change Password",
     *     security={{"bearer_token":{}}},
     *     operationId="change-password",
     *
     *     @OA\Parameter(
     *         name="current_password",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *
     *     @OA\Parameter(
     *         name="password",
     *         required=true,
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
    public function change_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false);
        }

        try {
            $user = User::find(Auth::id());
            if ($user) {
                if (Hash::check($request->current_password, $user->password)) {
                    $user->password = bcrypt($request->password);
                    $user->save();
                    return $this->response('', 'Password is updated succesfully.');
                } else {
                    return $this->response([], 'Old password is incorrect.', false, 401);
                }
            }
            return $this->response([], 'Enter Valid user name', false);

        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false);
        }
    }

    /**
     *  @OA\Post(
     *     path="/api/forgot/password",
     *     tags={"User"},
     *     summary="Forgot password",
     *     operationId="forgot-password",
     *
     *     @OA\Parameter(
     *         name="email",
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
     * )
     **/
    public function forgot_password(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        $user = User::where('email', $request->email)->first();
        if (empty($user)) {
            return $this->sendError('This email not registered');
        }

        try {
            $otp = rand(100000, 999999);
            $data = [
                'username' => $user->name,
                'otp' => $otp,
            ];
            UserOtp::where('user_id', $user->id)->delete();
            $saveOtp = new UserOtp;
            $saveOtp->user_id = $user->id;
            $saveOtp->otp = $otp;
            $saveOtp->save();
            $email = $user->email;
            $name = $user->name;
            Mail::send('mail.forgot', $data, function ($message) use ($email, $name) {
                $message->to($email, $name)->subject('Forgot Password');
            });
            return $this->response('', 'Email sent succesfully!');
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false, 404);
        }
    }
    /**
     *  @OA\Post(
     *     path="/api/update/password",
     *     tags={"User"},
     *     summary="Update password",
     *     operationId="update-password",
     *
     *     @OA\Parameter(
     *         name="otp",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *    @OA\Parameter(
     *         name="password",
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
     * )
     **/
    public function update_password(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'otp' => 'required',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {

            $userOtp = UserOtp::where('otp', $request->otp)->first();
            if ($userOtp) {
                $user = User::find($userOtp->user_id);
                $user->password = bcrypt($request->password);
                $user->save();
                $userOtp->delete();
                return $this->response('', 'Password is updated succesfully.');
            }
            return $this->response([], 'Enter valid otp!', false, 404);
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false, 404);
        }
    }

    /**
     *  @OA\Get(
     *     path="/api/profile",
     *     tags={"User"},
     *     security={{"bearer_token":{}}},
     *     summary="Get Login User Profile",
     *     operationId="profile",
     *
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
    public function me()
    {
        try {
            if (Auth::user()->type == 2) {
                $user = User::with(['brings', 'eligibles', 'industries', 'documents', 'fund'])->find(Auth::id());
            } else {
                $user = User::with('pledging')->find(Auth::id());
            }
            return $this->response($user, 'Profile!');
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false);
        }

    }

    /**
     * @OA\Get(
     *     path="/api/brings",
     *     tags={"User"},
     *     summary="Brings List",
     *     operationId="bring-list",
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
    public function bring_list(Request $request)
    {
        try {
            $brings = Bring::select('id', 'name')->get();
            return $this->response($brings, 'Brings list');
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false, 404);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/eligibles",
     *     tags={"User"},
     *     summary="Eligible List",
     *     operationId="eligible-list",
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
    public function eligible_list(Request $request)
    {
        try {
            $eligibles = Eligible::select('id', 'name')->get();
            return $this->response($eligibles, 'Eligible list');
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false, 404);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/industries",
     *     tags={"User"},
     *     summary="Industry List",
     *     operationId="industry-list",
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
    public function industry_list(Request $request)
    {
        try {
            $industries = Industry::select('id', 'name')->get();
            return $this->response($industries, 'Industries list');
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false, 404);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/username/check",
     *     tags={"User"},
     *     summary="check username exist",
     *     operationId="check-username",
     *
     *     @OA\Parameter(
     *         name="username",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
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
    public function check_username(Request $request)
    {
        if ($request->username != null) {
            $check_username = User::where('username', $request->username)->first();
            if ($check_username) {
                return $this->response([], 'This username already exist!', false, 409);
            }
            return $this->response([], 'This username exist!');
        }
        if ($request->email != null) {
            $check_user = User::where('email', $request->email)->first();
            if ($check_user) {
                return $this->response($check_user, 'User Exist!');
            }
            return $this->response([], 'This email user not exist!', false, 404);
        }
        return $this->response([], 'Please enter email or username!', false, 404);

    }

    /**
     * @OA\Post(
     *     path="/api/team/request",
     *     tags={"User"},
     *     summary="get team request user data",
     *     operationId="team-user",
     *
     *     @OA\Parameter(
     *         name="key",
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
    public function team_request_data(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {

            $user = TeamRequest::where('key', $request->key)->first();
            if (isset($user)) {
                return $this->response($user, 'Team request user data!');
            }
            return $this->response([], 'Enter valid key!', false, 404);
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false, 404);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/support",
     *     tags={"User"},
     *     summary="return email id for Support and feedback",
     *     operationId="support-email",
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
    public function support()
    {
        return $this->response('noreply.pitch@gmail.com', 'Email for support or feedback!');
    }

    /**
     * @OA\Post(
     *     path="/api/stripe/card/add",
     *     tags={"Stripe"},
     *     security={{"bearer_token":{}}},
     *     summary="get team request user data",
     *     operationId="stripe-card-add",
     *
     *     @OA\Parameter(
     *         name="card_number",
     *         in="query",
     *         required=true,
     *         description="4242424242424242",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="exp_month",
     *         in="query",
     *         required=true,
     *         description="1",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="exp_year",
     *         in="query",
     *         required=true,
     *         description="2023",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="cvc",
     *         in="query",
     *         required=true,
     *         description="314",
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
    public function stripe_card_add(Request $request)
    {

        Stripe::setApiKey(env('STRIPE_SECRET'));
        $stripeToken = Token::create([
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 1,
                'exp_year' => 2023,
                'cvc' => '314',
            ],
        ]);

        return $stripeToken;
    }

    /**
     * @OA\Get(
     *     path="/api/stripe/express/url",
     *     tags={"Stripe"},
     *     security={{"bearer_token":{}}},
     *     summary="get express url",
     *     operationId="stripe-express-url",
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
    public function stripe_express_url(Request $request)
    {

        Stripe::setApiKey(env('STRIPE_SECRET'));
        $user = User::find(Auth::id());
        if ($user->stripe_acc_id == null) {
            $account = Account::create([
                'type' => 'express',
                'capabilities' => [
                    'card_payments' => ['requested' => true],
                    'transfers' => ['requested' => true],
                ],
                'business_type' => 'individual',
            ]);
            $user->stripe_acc_id = $account->id;
            $user->save();
        }

        $link_url = AccountLink::create([
            'account' => $user->stripe_acc_id,
            'refresh_url' => 'http://localhost:8000/refresh',
            'return_url' => 'http://ingeniousmindslab.com/pitch/public/api/stripe?status=success',
            'type' => 'account_onboarding',
        ]);

        return $this->response(['url' => $link_url->url], 'Stripe link');
    }

    /**
     * @OA\Get(
     *     path="/api/stripe/account/status",
     *     tags={"Stripe"},
     *     security={{"bearer_token":{}}},
     *     summary="Stripe account status",
     *     operationId="stripe-account-status",
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
    public function retrive_acc(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $stripe_acc = Account::retrieve(Auth::user()->stripe_acc_id, []);

        if ($stripe_acc->capabilities->transfers == 'active') {
            $user = User::find(Auth::id());
            $user->acct_status = 2;
            $user->save();
            return $this->response($user, 'Stripe Active');
        }
        return $this->response($user, 'Pending verfication retry connect account!', false, 404);
    }

    /**
     *  @OA\Post(
     *     path="/api/team/add",
     *     tags={"User"},
     *     summary="Team add remove",
     *     security={{"bearer_token":{}}},
     *     operationId="team-add-remove",
     *
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         required=true,
     *         description="1-add | 2-remove",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *
     *      @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *
     *      @OA\Parameter(
     *         name="first_name",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *
     *      @OA\Parameter(
     *         name="last_name",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *
     *
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *
     *     @OA\Parameter(
     *         name="position",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *
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
    public function teamAdd(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'type' => 'required|in:1,2',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 400);
        }
        try {
            if ($request->type == 1) {
                if ($request->user_id == null) {

                    $teams = new TeamRequest;
                    $teams->business_id = Auth::id();
                    $teams->first_name = $request->first_name;
                    $teams->last_name = $request->last_name;
                    $teams->email = $request->email;
                    $teams->position = $request->position;
                    // $key = rand(10000, 99999) . time() . rand(10000, 99999);
                    $teams->key = $request->key;
                    $team->link = $request->link;
                    $teams->save();

                    $user = (object) ['email' => $request->email, 'first_name' => $request->first_name, 'last_name' => $request->last_name, 'position' => $request->position];
                    $link = $request->link;
                    $businessUser = User::find(Auth::id());
                    EmailJob::dispatch($user, "App\Mail\TeamRequestMail", ["user" => $user, "business" => $businessUser, "link" => $link]);
                    return $this->response('', 'Team request sent successfully.');
                } else {
                    $team = new TeamUser;
                    $team->business_id = Auth::id();
                    $team->user_id = $request->user_id;
                    $team->position = $request->position;
                    $team->save();
                    $user = User::find($request->user_id);
                    $businessUser = User::find(Auth::id());
                    EmailJob::dispatch($user, "App\Mail\WelcomeTeamMail", ["user" => $user, 'business' => $businessUser]);
                    $teamUser = User::find($request->user_id);
                    sendPushNotification($teamUser->device_token, $businessUser->startup_name . ' has added you to their team.', $businessUser->startup_name . ' has added you to their team.', 1, $teamUser->id);
                    return $this->response('', 'Team user added successfully.');
                }
            } else {

                $team_user = TeamUser::where('business_id', Auth::id())->where('user_id', $request->user_id)->first();
                if ($team_user) {
                    $team_user->delete();
                    return $this->response('', 'Team user deleted successfully.');
                }
                return $this->response([], 'Enter valid user id!.', 422);
            }
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false, 404);
        }
    }

}
