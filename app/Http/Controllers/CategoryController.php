<?php

namespace App\Http\Controllers;

use App\Category;
use App\CategoryLevel;
use App\CategoryMap;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // public function showAllCategory(Request $request)
    // {
    //     if ($request->name) {
    //         return response()->json(Category::where('name', 'LIKE', '%' . str_replace(' ', '%', $request->name) . '%')->get());
    //     } else {
    //         return response()->json(Category::all());
    //     }
    // }

    // public function showOneCategory($id)
    // {
    //     return response()->json(Category::find($id));
    // }

    // public function create(Request $request)
    // {
    //     $category = Category::create($request->all());
    //     $request = Request::create('/api/categorylevel?category_id=' . $category->id, 'POST');
    //     CategoryLevel::create($request->all());
    //     return response()->json($category, 201);
    // }

    // public function update($id, Request $request)
    // {
    //     if (empty(Category::find($id))) {
    //         return response([
    //             'success' => false,
    //             'message' => 'Invalid id',
    //         ], 400);
    //     }

    //     $category = Category::findOrFail($id);
    //     $category->update($request->all());
    //     return response()->json($category, 200);
    // }

    // public function delete($id)
    // {
    //     if (empty(Category::find($id))) {
    //         return response([
    //             'success' => false,
    //             'message' => 'Invalid id',
    //         ], 400);
    //     } else if (
    //         !empty(CategoryLevel::where('parent_category_id', $id)->first())
    //     ) {
    //         return response([
    //             'success' => false,
    //             'message' => 'Currently associated with sub category',
    //         ], 400);
    //     } else if (
    //         !empty(CategoryMap::where('category_id', $id)->first())
    //     ) {
    //         return response([
    //             'success' => false,
    //             'message' => 'Currently associated with product',
    //         ], 400);
    //     }

    //     Category::findOrFail($id)->delete();
    //     $categoryLevel = CategoryLevel::where('category_id', $id)->first();
    //     CategoryLevel::findOrFail($categoryLevel->id)->delete();

    //     return response([
    //         'success' => true,
    //         'message' => 'Deleted successfully',
    //     ], 200);
    // }
}

