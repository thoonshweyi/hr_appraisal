<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttachFormType extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "attach_form_types";
    protected $primaryKey = "id";
    protected $fillable = [
        "name",
        "slug",
        "status_id",
        "user_id"
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function status(){
        return $this->belongsTo(Status::class);
    }
}

// Reatiailမှာဆို Rank (2,3) (4,5)တွေခွဲပြီးတွဲပေးစရာမလိုတော့ပဲ Retailဆိုတဲ့ Form Groupနဲ့တွဲပေးထားရုံပါပဲ Employee ကRankတိုးသွားတဲ့အခါလိုက်ပြန်ပြင်စရာမလိုတော့ပဲ သူ့rankနဲ့ကိုက်ညီတဲ့Criteria အော်တိုရလာ။