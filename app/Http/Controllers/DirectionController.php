<?php

namespace App\Http\Controllers;

use App\Models\Direction;
use Illuminate\Http\Request;

class DirectionController extends Controller
{
    public function index()
    {
        return Direction::orderBy('description')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'description' => ['required', 'string', 'max:255'],
            'direction_generale' => ['nullable', 'string', 'max:255'],
        ]);
        return Direction::create($data);
    }

    public function update(Request $request, Direction $direction)
    {
        $data = $request->validate([
            'description' => ['sometimes', 'string', 'max:255'],
            'direction_generale' => ['sometimes', 'nullable', 'string', 'max:255'],
        ]);
        $direction->update($data);
        return $direction->refresh();
    }

    public function destroy(Direction $direction)
    {
        $direction->delete();
        return response()->noContent();
    }
}


