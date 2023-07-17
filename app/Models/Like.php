<?php

namespace App\Models;

use App\Models\Book;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    public function book(){
        return $this->belongsTo(Book::class);
    }
}
