<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        "image",
        "name",
        "desc",
        "price",
        "stock"
    ];


    public function transaction()
    {
        return $this->hasMany(Transaction::class);
    }
}
