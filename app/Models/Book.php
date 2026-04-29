<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Book extends Model
{
    use HasFactory;
    protected $fillable = [
        'title', 
        'author', 
        'price', 
        'stock', 
        'genre', 
        'year', 
        'description', 
        'image_url'
    ];
}
