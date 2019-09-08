<?php

namespace Vanguard;

use Illuminate\Database\Eloquent\Model;

class ParentCategory extends Model
{
    protected $table = 'parent_categories';

    public $timestamps = true;

    protected $guarded = [];
}
