<?php

namespace Vanguard;

use Illuminate\Database\Eloquent\Model;

class SkuGroup extends Model
{
    protected $table = 'sku_groups';

    public $timestamps = true;

    protected $guarded = [];
}
