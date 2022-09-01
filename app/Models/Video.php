<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\VideoLike;
Use App\Models\VideoSave;
Use App\Models\VideoView;
Use App\Models\User;
use Auth;

class Video extends Model
{
    use HasFactory;

    protected $appends = ['is_liked','total_like','total_view','is_saved'];

    public function getTotalLikeAttribute()
    {
        return $this->hasMany(VideoLike::class,'video_id')->count();
    }

    public function getTotalViewAttribute()
    {
        return $this->hasMany(VideoView::class,'video_id')->count();
    }

    public function getIsLikedAttribute()
    {
        $vidLike = VideoLike::where('video_id',$this->id)->where('user_id',Auth::id())->first();
        if(isset($vidLike)){
            return 1;
        }else {
            return 2;
        }
    }

    public function getIsSavedAttribute()
    {
        $vidSave = VideoSave::where('video_id',$this->id)->where('user_id',Auth::id())->first();
        if(isset($vidSave)){
            return 1;
        }else {
            return 2;
        }
    }

    public function upload_by()
    {
        return $this->hasOne(User::class,'id','user_id');
    }

    
}
