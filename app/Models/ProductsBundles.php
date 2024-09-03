<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductsBundles extends Model
{
    use HasFactory;

    protected $table = 'products_bundles';

    protected $fillable = [
        'ext_id',
        'quantity',
        'bundle_id',
        'product_id',
    ];

    public function bundle()
    {
        return $this->hasOne(Bundles::class, 'ext_id', 'bundle_id');
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'ext_id', 'product_id');
    }
}
