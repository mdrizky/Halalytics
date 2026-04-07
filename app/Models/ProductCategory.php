<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $table = 'marketplace_categories';

    protected $fillable = [
        'name',
        'icon',
    ];
}
