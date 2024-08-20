<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prices extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'type_name',
        'type_id',
        'value',
    ];

    public function product()
    {
        return $this->hasOne(Product::class, 'ext_id', 'product_id');
    }
}
