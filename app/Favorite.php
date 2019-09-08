<?php

namespace Vanguard;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    protected $table = 'favorites';

    public $timestamps = true;

    protected $guarded = [];
}
