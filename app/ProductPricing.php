<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductPricing extends Model
{
    /**
     * Bypass eloquent pluralization
     */
    protected $table = 'product_pricing';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'price',
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
    ];
}

