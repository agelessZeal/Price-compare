<?php

namespace Vanguard;

use Illuminate\Database\Eloquent\Model;

class RelatedProduct extends Model
{
    protected $table = 'related_products';

    public $timestamps = true;

    protected $guarded = [];
}
