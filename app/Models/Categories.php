<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    use HasFactory;
    protected $fillable = ['name'];
    protected $table = 'categories';
    protected $hidden = ['created_at', 'updated_at'];

    public function products(){
        return $this->hasMany(Products::class);
    }
}
