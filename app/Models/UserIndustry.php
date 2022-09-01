<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class UserIndustry extends Model
{
    use HasFactory;

    public function detail()
    {
        return $this->hasOne(Eligible::class,'id','eligible_id');
    }

    public function business()
    {
        return $this->hasOne(User::class,'id','user_id');
    }
}
