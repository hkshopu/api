<?php

namespace App;

use App\Category;
use App\Status;
use App\StatusMap;
use Illuminate\Database\Eloquent\Model;

class CategoryLevel extends Model
{
    /**
     * Bypass eloquent pluralization
     */
    protected $table = 'category_level';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'parent_category_id',
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

    public static function buildTree(array $elements, $parentId = 0) {
        $branch = [];

        foreach ($elements as $element) {
            if ($element['parent_category_id'] == $parentId) {
                $children = self::buildTree($elements, $element['category']->id);
                if ($children) {
                    $element['sub_category'] = $children;
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }

    public static function buildRoot(array $element) {

        $category = new Category();
        $categoryEntity = Entity::where('name', $category->getTable())->first();

        $categoryLevel = self::where('category_id', $element['category']['id'])->whereNull('deleted_at')->first();

        if (!empty($element['sub_category'])) {
            $subcategory = $element['sub_category'];
            unset($element['sub_category']);
        }

        $element['category_id'] = $element['category']['id'];
        $element['category'] = $element['category']['name'];
        $element['parent_category_id'] = $categoryLevel->parent_category_id;
        
        if (!empty($subcategory)) {
            $element['sub_category'] = $subcategory;
        } else {
            $element['sub_category'] = [];
        }

        while ($element['parent_category_id'] <> 0) {
            $parentElement = [];
            $parentElement['category'] = Category::where('id', $element['parent_category_id'])->whereNull('deleted_at')->first()->toArray();
            $parentElement['sub_category'][] = $element;
            $element = self::buildRoot($parentElement);
        }

        return $element;
    }
}

