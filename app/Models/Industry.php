<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UserIndustry;

class Industry extends Model
{
    use HasFactory;

    public function companies()
    {
        return $this->hasMany(UserIndustry::class,'industry_id')->with('business');
    }
}
