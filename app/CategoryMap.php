<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoryMap extends Model
{
    /**
     * Bypass eloquent pluralization
     */
    protected $table = 'category_map';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'entity',
        'entity_id',
        'category_id',
        //
        'created_by',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'entity',
        'entity_id',
    ];
}

