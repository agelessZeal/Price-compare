<?php

namespace Vanguard;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    public $timestamps = true;

    protected $guarded = [];
}
