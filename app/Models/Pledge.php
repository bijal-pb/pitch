<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Support\Carbon;

class Pledge extends Model
{
    use HasFactory;

    public function pledge_by()
    {
        return $this->hasOne(User::class,'id','from_user');
    }

    public function pledge_to()
    {
        return $this->hasOne(User::class,'id','to_user');
    }
    
    protected $appends = ['status_type'];

    public function getStatusTypeAttribute()
    {
        if($this->status == 1){
            return 'Processing';
        }
        if($this->status == 2){
            return 'Completed';
        }
        if($this->status == 3){
            return 'Declined';
        }
        if($this->status == 4){
            return 'Refund';
        }
    }

    public function getUpdatedAtAttribute($value)
    {
        return (new Carbon($value))->format('Y-m-d H:i:s');
    }
}
