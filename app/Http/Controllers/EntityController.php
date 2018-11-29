<?php

namespace App\Http\Controllers;

use App\Entity;
use Illuminate\Http\Request;

class EntityController extends Controller
{
    // public function showAllEntity()
    // {
    //     return response()->json(Entity::all());
    // }

    // public function showOneEntity($id)
    // {
    //     return response()->json(Entity::find($id));
    // }

    // public function create(Request $request)
    // {
    //     $entity = Entity::create($request->all());

    //     return response()->json($entity, 201);
    // }

    // public function update($id, Request $request)
    // {
    //     $entity = Entity::findOrFail($id);
    //     $entity->update($request->all());

    //     return response()->json($entity, 200);
    // }

    // public function delete($id)
    // {
    //     Entity::findOrFail($id)->delete();
    //     return response('Deleted Successfully', 200);
    // }
}

