<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $table = 'stocks';

    protected $fillable = [
        'product_id',
        'store_id',
        'name',
        'stock',
        'reserve',
        'in_transit',
    ];

    public function product()
    {
        return $this->hasOne(Product::class, 'ext_id', 'product_id');
    }

    public function store()
    {
        return $this->hasOne(Stores::class, 'ext_id', 'store_id');
    }
}
