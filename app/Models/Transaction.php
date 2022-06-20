<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'food_id', 'quantity', 'total', 'status',
        'payment_url'
    ];

    public function food(){
        $this->hasOne(Food::class, 'id', 'food_id');
    }
    public function user(){
        $this->hasOne(User::class, 'id', 'user_id');
    }

    public function getCreatedAtAttribute($val){
        return Carbon::parse($val)->timestamp;
    }
    public function getyUpdatedAtAttribute($val){
        return Carbon::parse($val)->timestamp;
    }

}
