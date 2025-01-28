<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{

    public function index()
    {
        return Tag::all();
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $tag = Tag::create(['name' => $request->name]);
        return response()->json($tag, 201);
    }

    public function show(Tag $tag)
    {
        return $tag;
    }

    public function update(Request $request, Tag $tag)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
    ]);

    $tag->update($validated);
    return response()->json($tag, 200);
}

    public function destroy(Tag $tag)
    {
        $tag->delete();
        return response()->json(null, 204);
    }

}
