<?php

namespace App\Http\Controllers;

use App\Models\TypeAlerte;
use Illuminate\Http\Request;

class TypeAlerteController extends Controller
{
    public function index()
    {
        return TypeAlerte::orderBy('nom')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255', 'unique:types_alertes,nom'],
            'description' => ['nullable', 'string'],
        ]);
        return TypeAlerte::create($data);
    }

    public function update(Request $request, TypeAlerte $type)
    {
        $data = $request->validate([
            'nom' => ['sometimes', 'string', 'max:255', 'unique:types_alertes,nom,' . $type->id],
            'description' => ['sometimes', 'nullable', 'string'],
        ]);
        $type->update($data);
        return $type->refresh();
    }

    public function destroy(TypeAlerte $type)
    {
        $type->delete();
        return response()->noContent();
    }
}


