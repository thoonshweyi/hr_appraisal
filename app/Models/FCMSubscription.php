<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FCMSubscription extends Model
{
    use HasFactory;
    protected $table = "f_c_m_subscriptions";
    protected $primaryKey = "id";
    protected $fillable = [
        "user_id",
        "fcm_token",
        "status_id"
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function status(){
        return $this->belongsTo(Status::class);
    }
}
