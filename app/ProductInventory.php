<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ProductAttribute;

class ProductInventory extends Model
{
    /**
     * Bypass eloquent pluralization
     */
    protected $table = 'product_inventory';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_attribute_id',
        'stock',
        'order_id',
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

    public static function checkStock(int $productId, int $attributeId) {
        $productAttribute = ProductAttribute::where('product_id', $productId)->where('attribute_id', $attributeId)->whereNull('deleted_at')->first();

        $stock = app('App\Http\Controllers\ProductController')->getStock($productAttribute->id);

        return $stock;
    }
}

