<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StatusMap extends Model
{
    /**
     * Bypass eloquent pluralization
     */
    protected $table = 'status_map';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'entity',
        'entity_id',
        'status_id',
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

