<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    use HasFactory;

    protected $appends = ['status_type'];
    public function getStatusTypeAttribute()
    {
        if($this->status == 1){
            return  'Processing';
        }
        if($this->status == 2){
            return 'Refunded';
        }
        if($this->status == 3){
            return 'Decline';
        }
    }
}
