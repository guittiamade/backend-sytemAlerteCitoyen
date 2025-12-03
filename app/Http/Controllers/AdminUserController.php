<?php

namespace App\Http\Controllers;

use App\Models\Direction;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(): View
    {
        $users = User::orderByDesc('id')->paginate(10);
        $profiles = Profile::orderBy('nom')->get();
        $directions = Direction::orderBy('description')->get();
        return view('admin.users', compact('users', 'profiles', 'directions'));
    }

    public function create(): View
    {
        $profiles = Profile::orderBy('nom')->get();
        $directions = Direction::orderBy('description')->get();
        return view('admin.user_create', compact('profiles','directions'));
    }

    public function store(Request $request): RedirectResponse
    {
        if (!$request->filled('email')) {
            $request->merge(['email' => null]);
        }

        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['nullable','email','max:255','unique:users,email'],
            'password' => ['required','string','min:6'],
            'tel' => ['required','string','max:30','unique:users,tel'],
            'profile_id' => ['required','integer','exists:profiles,id'],
            'direction_id' => ['nullable','integer','exists:directions,id'],
        ]);
        User::create($data);
        return redirect()->route('admin.users')->with('ok', 'Utilisateur créé');
    }

    public function edit(User $user): View
    {
        $profiles = Profile::orderBy('nom')->get();
        $directions = Direction::orderBy('description')->get();
        return view('admin.user_edit', compact('user','profiles','directions'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        if ($request->has('email') && !$request->filled('email')) {
            $request->merge(['email' => null]);
        }

        $data = $request->validate([
            'name' => ['sometimes','required','string','max:255'],
            'email' => ['sometimes','nullable','email','max:255','unique:users,email,' . $user->id],
            'password' => ['nullable','string','min:6'],
            'tel' => ['sometimes','required','string','max:30','unique:users,tel,' . $user->id],
            'profile_id' => ['sometimes','required','integer','exists:profiles,id'],
            'direction_id' => ['nullable','integer','exists:directions,id'],
        ]);
        if (empty($data['password'])) unset($data['password']);
        $user->update($data);
        return redirect()->route('admin.users')->with('ok', 'Utilisateur mis à jour');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();
        return redirect()->route('admin.users')->with('ok', 'Utilisateur supprimé');
    }

    public function gestionnaires(): View
    {
        $users = User::whereHas('profile', fn($q)=>$q->where('nom','gestionnaire'))
            ->orderBy('name')->paginate(10);
        $profiles = Profile::orderBy('nom')->get();
        $directions = Direction::orderBy('description')->get();
        return view('admin.gestionnaires', compact('users','profiles','directions'));
    }

    public function createGestionnaire(): View
    {
        $profiles = Profile::orderBy('nom')->get();
        $directions = Direction::orderBy('description')->get();
        return view('admin.gestionnaire_create', compact('profiles','directions'));
    }
}
