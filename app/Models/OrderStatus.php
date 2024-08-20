<?php

namespace App\Models;

enum OrderStatus: string
{
    case Active = 'active';
    case Completed = 'completed';
    case Canceled = 'canceled';
}
