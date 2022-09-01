<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;
use App\Models\UserBring;
use App\Models\UserEligible;
use App\Models\UserIndustry;
use App\Models\UserDocument;
use App\Models\Follower;
use App\Models\Fund;
use App\Models\Pledge;
use Auth;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles;

    protected $appends = ['total_following','total_follower','total_pledges'];



    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'first_name',
        'last_name'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function brings()
    {
        return $this->hasMany(UserBring::class,'user_id')->with('detail');
    }

    public function eligibles()
    {
        return $this->hasMany(UserEligible::class,'user_id')->with('detail');
    }

    public function industries()
    {
        return $this->hasMany(UserIndustry::class,'user_id')->with('detail');
    }

    public function documents()
    {
        return $this->hasMany(UserDocument::class,'user_id');
    }

    public function fund()
    {
        return $this->hasOne(Fund::class,'user_id','id');
    }

    public function pledging()
    {
        return $this->hasMany(Pledge::class,'from_user')->with('pledge_to')->orderBy('created_at','desc');
    }

    public function getTotalFollowingAttribute()
    {
        return $this->hasMany(Follower::class,'follow_by')->count();
    }

    public function getTotalFollowerAttribute()
    {
        return $this->hasMany(Follower::class,'follow_to')->count();
    }

    public function getTotalPledgesAttribute()
    {
        return $this->hasMany(Pledge::class,'to_user')->where('status',2)->count();
    }

    public function all_uploaded_videos()
    {
        return $this->hasMany(Video::class,'user_id');
    }

    public function team_user()
    {
        return $this->hasMany(TeamUser::class,'business_id')->with('user');
    }

    
}
