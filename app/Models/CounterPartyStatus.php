<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CounterPartyStatus extends Model
{
    use HasFactory;

    protected $table = 'counterparty_status';

    protected $fillable = [
        'ext_id',
        'name',
        'color',
        'state_type',
    ];
}
