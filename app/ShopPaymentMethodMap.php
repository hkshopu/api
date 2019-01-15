<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShopPaymentMethodMap extends Model
{
    /**
     * Bypass eloquent pluralization
     */
    protected $table = 'shop_payment_method_map';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'shop_id',
        'payment_method_id',
        'account_info',
        'remarks',
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

