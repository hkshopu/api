<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class View extends Model
{
    /**
     * Bypass eloquent pluralization
     */
    protected $table = 'view';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'entity',
        'entity_id',
        'ip_address',
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
        'created_at',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];
}

