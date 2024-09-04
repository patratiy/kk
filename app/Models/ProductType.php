<?php

namespace App\Models;

enum ProductType: string
{
    case Product = 'product';
    case Bundle = 'bundle';
}
