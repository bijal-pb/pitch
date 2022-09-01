<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\VideoSave;
use App\Models\VideoLike;
use App\Models\VideoView;
use App\Models\User;
use App\Models\CompanySave;
use App\Models\Follower;
use App\Traits\ApiTrait;
use Exception;
use Illuminate\Support\Facades\Validator;
use Auth;


class VideoController extends Controller
{
    use ApiTrait;

    /**
     * @OA\Post(
     *     path="/api/video/add",
     *     tags={"Video"},
     *     security={{"bearer_token":{}}},  
     *     summary="add new video business user",
     *     operationId="video-add",
     * 
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),  
     *    @OA\Parameter(
     *         name="video",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ), 
     *     @OA\Parameter(
     *         name="thumbnail",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ), 
     *      @OA\Parameter(
     *         name="length",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),  
     *     @OA\Parameter(
     *         name="detail",
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
    public function video_add(Request $request){
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:1,2',
            'video' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false,400);
        }

        try{
            $video = new Video;
            $video->user_id = Auth::id();
            $video->type = $request->type;
            $video->video = $request->video;
            $video->thumbnail = $request->thumbnail;
            $video->length = $request->length;
            $video->detail = $request->detail;
            $video->save();

            $followers = Follower::where('follow_to',Auth::id())->pluck('follow_by')->toArray();
            $user_tokens = User::whereIn('id',$followers)->where('is_notification',1)->pluck('device_token')->toArray();
            sendPushNotification($user_tokens,Auth::user()->startup_name.' has uploaded a new video.',Auth::user()->startup_name.' has uploaded a new video.',1,null,null,$video->id);
            return $this->response($video, 'Video uploaded successfully!');
        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false,404);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/video/save",
     *     tags={"Video"},
     *     security={{"bearer_token":{}}},  
     *     summary="save video pledge user",
     *     operationId="video-save",
     * 
     *    @OA\Parameter(
     *         name="video_id",
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
    public function video_save(Request $request){
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:1,2',
            'video_id' => 'required|exists:videos,id'
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false,400);
        }

        try{
            $checkSave = VideoSave::where('user_id',Auth::id())->where('video_id',$request->video_id)->first();
            if($request->type == 1){
                if(!isset($checkSave)){
                    $video = new VideoSave;
                    $video->user_id = Auth::id();
                    $video->video_id = $request->video_id;
                    $video->save();
                    return $this->response($video, 'Video saved successfully!');
                }
                return $this->response('', 'Video already saved!');
            }
            if($request->type == 2){
                if(isset($checkSave)){
                    $checkSave->delete();
                }
                return $this->response('', 'Video removed successfully!');
            }
        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false,404);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/video/like",
     *     tags={"Video"},
     *     security={{"bearer_token":{}}},  
     *     summary="like video pledge user",
     *     operationId="video-like",
     * 
     *    @OA\Parameter(
     *         name="video_id",
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
     *         description="1 - like | 2 -remove",
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
    public function video_like(Request $request){
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:1,2',
            'video_id' => 'required|exists:videos,id'
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false,400);
        }

        try{
            $checkLike = VideoLike::where('user_id',Auth::id())->where('video_id',$request->video_id)->first();
            if($request->type == 1){
                if(!isset($checkLike)){
                    $video = new VideoLike;
                    $video->user_id = Auth::id();
                    $video->video_id = $request->video_id;
                    $video->save();

                    $vid = Video::find($request->video_id);
                    $user = User::find($vid->user_id);
                    sendPushNotification($user->device_token,Auth::user()->name.' has liked your video.',Auth::user()->name.' has liked your video.',1,$user->id,null,$vid->id);
                    return $this->response($video, 'Video liked successfully!');
                }
                return $this->response($video, 'Video already liked!');
            }
            if($request->type == 2){
                if(isset($checkLike)){
                    $checkLike->delete();
                }
                return $this->response('', 'Video disliked successfully!');
            }
        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false,404);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/video/view",
     *     tags={"Video"},
     *     security={{"bearer_token":{}}},  
     *     summary="view video pledge user",
     *     operationId="video-view",
     * 
     *    @OA\Parameter(
     *         name="video_id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
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
    public function video_view(Request $request){
        $validator = Validator::make($request->all(), [
            'video_id' => 'required|exists:videos,id'
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false,400);
        }

        try{
            $checkLike = VideoView::where('user_id',Auth::id())->where('video_id',$request->video_id)->first();
            if(!isset($checkLike)){
                $video = new VideoView;
                $video->user_id = Auth::id();
                $video->video_id = $request->video_id;
                $video->save();
                return $this->response($video, 'Video viewd successfully!');
            }
            return $this->response([], 'Video already viewed!');
        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false,404);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/video/save/list",
     *     tags={"Video"},
     *     summary="Save video list",
     *     security={{"bearer_token":{}}},
     *     operationId="video-save-list",
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
    public function video_save_list(Request $request)
    {
        try{
            $videoIds = VideoSave::where('user_id',Auth::id())->pluck('video_id')->toArray();
            $videos = Video::with('upload_by')->whereIn('id',$videoIds)->where('type',1)->get();
            $tv = Video::with('upload_by')->whereIn('id',$videoIds)->where('type',2)->get();

            $companyIds = CompanySave::where('user_id',Auth::id())->pluck('company_id')->toArray();
            $company = User::whereIn('id',$companyIds)->get();
            return $this->response([
                'videos' => $videos,
                'tv' => $tv,
                'companies' => $company
            ], 'Saved videos list!');
        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false,404);
        }
    }
}