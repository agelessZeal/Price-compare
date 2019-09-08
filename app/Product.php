<?php

namespace Vanguard;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    public $timestamps = true;

    protected $guarded = [];
}
