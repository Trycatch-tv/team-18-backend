<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $hidden = ['created_at', 'updated_at'];

    public function cat(){
        return $this->hasOne(Category::class, 'id', 'category_id');
    }
}
