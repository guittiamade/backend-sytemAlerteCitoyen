<?php

namespace App\Http\Controllers;

use App\Models\TypeAlerte;
use Illuminate\Http\Request;

class AdminTypeAlerteController extends Controller
{
    public function index(Request $request)
    {
        $q = TypeAlerte::query();
        if ($s = trim((string)$request->query('q'))) {
            $q->where(function ($sub) use ($s) {
                $sub->where('nom', 'like', "%{$s}%")
                    ->orWhere('description', 'like', "%{$s}%");
            });
        }
        $types = $q->orderBy('nom')->paginate(10)->withQueryString();
        return view('admin.types', compact('types'));
    }

    public function create()
    {
        return view('admin.type_create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255', 'unique:types_alertes,nom'],
            'description' => ['nullable', 'string'],
        ]);
        TypeAlerte::create($data);
        return redirect()->route('admin.types')->with('ok', 'Type ajouté');
    }

    public function update(Request $request, TypeAlerte $type)
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255', 'unique:types_alertes,nom,' . $type->id],
            'description' => ['nullable', 'string'],
        ]);
        $type->update($data);
        return redirect()->route('admin.types')->with('ok', 'Type mis à jour');
    }

    public function destroy(TypeAlerte $type)
    {
        $type->delete();
        return redirect()->route('admin.types')->with('ok', 'Type supprimé');
    }

    public function edit(TypeAlerte $type)
    {
        return view('admin.type_edit', compact('type'));
    }
}


