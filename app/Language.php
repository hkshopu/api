<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Language extends Model
{
    /**
     * Bypass eloquent pluralization
     */
    protected $table = 'language';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'name',
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

    public static function translate(Request $request, $entity, string $field) {
        $languageList = self::whereNull('deleted_at')->orderBy('id', 'ASC')->get();

        $arrayRequest = $request->all();
        $arrayEntity = $entity->toArray();

        if (!empty($arrayEntity[$field . '_' . $arrayRequest['language']])) {
            return $arrayEntity[$field . '_' . $arrayRequest['language']];
        } else {
            foreach ($languageList as $langauge) {
                if (!empty($arrayEntity[$field . '_' . $langauge->code])) {
                    return $arrayEntity[$field . '_' . $langauge->code];
                }
            }
        }

        return null;
    }
}

