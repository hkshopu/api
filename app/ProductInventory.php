<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
        'product_id',
        'attribute_id',
        'stock',
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
        $productInventoryList = self::where('product_id', $productId)->where('attribute_id', $attributeId)->whereNull('deleted_at')->get();

        $productStock = null;

        foreach ($productInventoryList as $productInventory) {
            $productStock += $productInventory->stock;
        }

        return $productStock;
    }
}

