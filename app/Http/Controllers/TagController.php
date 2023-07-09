<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::all();

        return response()->json($tags);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:tags|string',
        ]);

        $tag = Tag::create($validatedData);

        return response()->json($tag);
    }

    public function update(Request $request, Tag $tag)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|unique:tags,name,' . $tag->id,
        ]);

        $tag->update($validatedData);

        return response()->json($tag);
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();

        return response()->json('Tag deleted successfully');
    }
}
