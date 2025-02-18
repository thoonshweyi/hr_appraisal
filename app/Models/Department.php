<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'branch_id'
    ];
    public function branches(){
        return $this->belongsTo('App\Models\Branch','branch_id');
    }
}
