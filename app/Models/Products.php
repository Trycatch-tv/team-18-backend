<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price', 'category_id', 'stock', 'description', 'image', 'public_id', 'user_id'];
    protected $table = 'products';
    protected $hidden = ['created_at', 'updated_at', 'category_id', 'public_id', 'user_id'];

    public function category(){
        return $this->belongsTo(Categories::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
