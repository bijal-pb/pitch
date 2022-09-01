<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Bring;

class UserBring extends Model
{
    use HasFactory;

    public function detail()
    {
        return $this->hasOne(Bring::class,'id','bring_id');
    }
}
