<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    /**
     * Bypass eloquent pluralization
     */
    protected $table = 'rating';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'entity',
        'entity_id',
        'rate',
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

    public static function getAverage($entityObject) {
        $entity = Entity::where('name', $entityObject->getTable())->first();

        $list = self::where('entity', $entity->id)->where('entity_id', $entityObject->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->get();

        $sum = 0;
        $count = 0;
        foreach ($list as $item) {
            $sum += $item['rate'];
            $count++;
        }

        if ($count == 0) {
            $count = 1;
        }

        $average = $sum / $count;

        return $average;
    }
}

