<?php

namespace App\Http\Controllers;

use App\Models\Direction;
use Illuminate\Http\Request;

class AdminDirectionController extends Controller
{
    public function index(Request $request)
    {
        $q = Direction::query();
        if ($s = trim((string)$request->query('q'))) {
            $q->where(function ($sub) use ($s) {
                $sub->where('description', 'like', "%{$s}%")
                    ->orWhere('direction_generale', 'like', "%{$s}%");
            });
        }
        $directions = $q->orderBy('description')->paginate(10)->withQueryString();
        return view('admin.directions', compact('directions'));
    }

    public function create()
    {
        return view('admin.direction_create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'description' => ['required', 'string', 'max:255'],
            'direction_generale' => ['nullable', 'string', 'max:255'],
        ]);
        Direction::create($data);
        return redirect()->route('admin.directions')->with('ok', 'Direction ajoutée');
    }

    public function update(Request $request, Direction $direction)
    {
        $data = $request->validate([
            'description' => ['required', 'string', 'max:255'],
            'direction_generale' => ['nullable', 'string', 'max:255'],
        ]);
        $direction->update($data);
        return redirect()->route('admin.directions')->with('ok', 'Direction mise à jour');
    }

    public function edit(Direction $direction)
    {
        return view('admin.direction_edit', compact('direction'));
    }

    public function destroy(Direction $direction)
    {
        $direction->delete();
        return redirect()->route('admin.directions')->with('ok', 'Direction supprimée');
    }
}


