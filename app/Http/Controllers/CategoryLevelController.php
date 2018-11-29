<?php

namespace App\Http\Controllers;

use App\CategoryLevel;
use App\Category;
use Illuminate\Http\Request;

class CategoryLevelController extends Controller
{
    // public function showAllCategoryLevel(Request $request)
    // {
    //     $categoryId = $request->category_id ?? null;

    //     $elements = [];
    //     $categoryLevelList = CategoryLevel::all();
    //     foreach ($categoryLevelList as $categoryLevelKey => $categoryLevelItem) {
    //         $elements[$categoryLevelKey]['category'] = Category::find($categoryLevelItem->category_id);
    //         $elements[$categoryLevelKey]['parent_category_id'] = $categoryLevelItem->parent_category_id;
    //     }

    //     $tree = CategoryLevel::buildTree($elements, $categoryId);

    //     if ($categoryId) {
    //         $category = Category::find($categoryId);
    //         if (empty($category)) {
    //             return response([
    //                 'success' => false,
    //                 'message' => 'Invalid category id',
    //             ], 400);
    //         }
    //         $data['category'] = $category;
    //         $categoryLevel = CategoryLevel::where('category_id', $categoryId)->first();
    //         $data['parent_category_id'] = $categoryLevel->parent_category_id;
    //         $data['subcategory'] = $tree;
    //     } else {
    //         $data = $tree;
    //     }

    //     return response()->json($data);
    // }

    // public function update($category_id, Request $request)
    // {
    //     if (empty(Category::find($category_id))) {
    //         return response([
    //             'success' => false,
    //             'message' => 'Invalid category id',
    //         ], 400);
    //     }

    //     $categoryLevel = CategoryLevel::where('category_id', $category_id)->first();
    //     if (empty($request->parent_category_id)) {
    //         $request->request->add([
    //             'parent_category_id' => null,
    //         ]);
    //     } else if (empty(Category::find($request->parent_category_id))) {
    //         return response([
    //             'success' => false,
    //             'message' => 'Invalid parent category id',
    //         ], 400);
    //     }

    //     $categoryLevel->update($request->all());
    //     return response([
    //         'success' => true,
    //         'message' => 'Updated successfully',
    //     ], 200);
    // }
}

