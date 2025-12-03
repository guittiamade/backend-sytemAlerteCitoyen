<?php

namespace App\Http\Controllers;

use App\Models\TypeAlerte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'image' => ['nullable', 'image', 'max:2048'], // 2MB max
        ]);

        // Gestion de l'upload de l'image
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('types-alertes', 'public');
            $data['image'] = $path;
        } else {
            $data['image'] = null;
        }

        TypeAlerte::create($data);
        return redirect()->route('admin.types')->with('ok', 'Type ajouté');
    }

    public function update(Request $request, TypeAlerte $type)
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255', 'unique:types_alertes,nom,' . $type->id],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'], // 2MB max
            'remove_image' => ['nullable', 'boolean'],
        ]);

        // Gestion de la suppression de l'image
        if ($request->has('remove_image') && $request->remove_image) {
            // Supprimer l'ancienne image si elle existe
            if ($type->image) {
                Storage::disk('public')->delete($type->image);
                $data['image'] = null;
            }
        } 
        // Gestion du téléchargement d'une nouvelle image
        elseif ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($type->image) {
                Storage::disk('public')->delete($type->image);
            }
            
            // Enregistrer la nouvelle image
            $path = $request->file('image')->store('types-alertes', 'public');
            $data['image'] = $path;
        } else {
            // Conserver l'image existante si aucune nouvelle n'est téléchargée
            unset($data['image']);
        }

        $type->update($data);
        return redirect()->route('admin.types')->with('ok', 'Type mis à jour');
    }

    public function destroy(TypeAlerte $type)
    {
        // Supprimer l'image associée si elle existe
        if ($type->image) {
            Storage::disk('public')->delete($type->image);
        }
        
        $type->delete();
        return redirect()->route('admin.types')->with('ok', 'Type supprimé');
    }

    public function edit(TypeAlerte $type)
    {
        return view('admin.type_edit', compact('type'));
    }
}


