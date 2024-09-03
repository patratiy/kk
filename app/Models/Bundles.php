<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bundles extends Model
{
    use HasFactory;

    protected $table = 'bundles';

    protected $fillable = [
        'ext_id',
        'code',
        'ext_code',
        'name',
        'article',
        'group_name',
        'brand',
    ];
}
