<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CounterParty extends Model
{
    use HasFactory;

    protected $fillable = [];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
