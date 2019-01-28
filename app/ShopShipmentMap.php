<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShopShipmentMap extends Model
{
    /**
     * Bypass eloquent pluralization
     */
    protected $table = 'shop_shipment_map';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'shop_id',
        'shipment_id',
        'amount',
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
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];
}

