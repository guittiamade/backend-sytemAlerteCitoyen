<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminProfileController extends Controller
{
    public function index(Request $request): View
    {
        $q = Profile::query();
        if ($s = trim((string)$request->query('q'))) {
            $q->where(function ($sub) use ($s) {
                $sub->where('nom', 'like', "%{$s}%")
                    ->orWhere('description', 'like', "%{$s}%");
            });
        }
        $profiles = $q->orderBy('nom')->paginate(10)->withQueryString();
        return view('admin.profiles', compact('profiles'));
    }

    public function create(): View
    {
        return view('admin.profile_create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nom' => ['required','string','max:255','unique:profiles,nom'],
            'description' => ['nullable','string','max:255'],
        ]);
        Profile::create($data);
        return redirect()->route('admin.profiles')->with('ok','Profil créé');
    }

    public function update(Request $request, Profile $profile): RedirectResponse
    {
        $data = $request->validate([
            'nom' => ['required','string','max:255','unique:profiles,nom,' . $profile->id],
            'description' => ['nullable','string','max:255'],
        ]);
        $profile->update($data);
        return redirect()->route('admin.profiles')->with('ok','Profil mis à jour');
    }

    public function edit(Profile $profile): View
    {
        return view('admin.profile_edit', compact('profile'));
    }

    public function destroy(Profile $profile): RedirectResponse
    {
        if ($profile->nom === 'super_admin') {
            return redirect()->route('admin.profiles')->with('error','Impossible de supprimer le profil super_admin');
        }
        if ($profile->users()->exists()) {
            return redirect()->route('admin.profiles')->with('error','Impossible de supprimer: des utilisateurs utilisent ce profil');
        }
        $profile->delete();
        return redirect()->route('admin.profiles')->with('ok','Profil supprimé');
    }
}
