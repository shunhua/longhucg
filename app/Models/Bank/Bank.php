<?php

namespace App\Models\Bank;

use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Bank extends Model implements Sortable
{  
	use SortableTrait;

	/**
     * 可以被集体附值的表的字段
     */
    protected $guarded  = [];

    public $sortable = [
        'order_column_name' => 'order',
        'sort_when_creating' => true,
    ];

}

