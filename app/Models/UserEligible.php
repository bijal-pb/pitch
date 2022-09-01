<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Eligible;

class UserEligible extends Model
{
    use HasFactory;

    public function detail()
    {
        return $this->hasOne(Eligible::class,'id','eligible_id');
    }
}
