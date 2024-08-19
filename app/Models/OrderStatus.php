<?php

namespace App\Models;

enum Status: string
{
    case Active = 'active';
    case Completed = 'completed';
    case Canceled = 'canceled';
}
