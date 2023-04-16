<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'user_id'];
    protected $table = 'categories';
    protected $hidden = ['created_at', 'updated_at', 'user_id'];

    public function products(){
        return $this->hasMany(Products::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
