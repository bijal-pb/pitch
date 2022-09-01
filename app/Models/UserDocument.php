<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDocument extends Model
{
    use HasFactory;

    protected $appends = ['document_type'];

    public function getDocumentTypeAttribute()
    {
        if($this->type == 1){
            return 'Government';
        }
        if($this->type == 2){
            return 'Address';
        }
        if($this->type == 3){
            return 'Startup';
        }
    }
}
