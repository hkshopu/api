<?php

namespace App\Http\Controllers;

use App\Color;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    // public function showAllColor()
    // {
    //     return response()->json(Color::all());
    // }

    // public function showOneColor($id)
    // {
    //     return response()->json(Color::find($id));
    // }

    // public function create(Request $request)
    // {
    //     $color = Color::create($request->all());

    //     return response()->json($color, 201);
    // }

    // public function update($id, Request $request)
    // {
    //     $color = Color::findOrFail($id);
    //     $color->update($request->all());

    //     return response()->json($color, 200);
    // }

    // public function delete($id)
    // {
    //     Color::findOrFail($id)->delete();
    //     return response('Deleted Successfully', 200);
    // }
}

