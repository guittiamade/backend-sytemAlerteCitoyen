@extends('admin.layout')
@section('title','Créer un utilisateur')
@section('content')
<div class="max-w-3xl bg-white rounded-xl border border-slate-200 shadow-sm p-6">
  <h1 class="text-xl font-semibold mb-4">Créer un utilisateur</h1>
  <form method="post" action="{{ route('admin.users.store') }}" class="grid md:grid-cols-2 gap-4">
    @csrf
    <div>
      <label class="block text-sm text-slate-700 mb-1">Nom</label>
      <input name="name" value="{{ old('name') }}" required class="w-full px-3 py-2 rounded-md border border-slate-300 focus:outline-none focus:ring-2 focus:ring-sky-500">
      @error('name')<div class="mt-1 text-sm text-rose-600">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="block text-sm text-slate-700 mb-1">Email (optionnel)</label>
      <input type="email" name="email" value="{{ old('email') }}" class="w-full px-3 py-2 rounded-md border border-slate-300 focus:outline-none focus:ring-2 focus:ring-sky-500">
      @error('email')<div class="mt-1 text-sm text-rose-600">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="block text-sm text-slate-700 mb-1">Mot de passe</label>
      <input type="password" name="password" required class="w-full px-3 py-2 rounded-md border border-slate-300 focus:outline-none focus:ring-2 focus:ring-sky-500">
      @error('password')<div class="mt-1 text-sm text-rose-600">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="block text-sm text-slate-700 mb-1">Téléphone</label>
      <input type="tel" name="tel" value="{{ old('tel') }}" required class="w-full px-3 py-2 rounded-md border border-slate-300 focus:outline-none focus:ring-2 focus:ring-sky-500">
      @error('tel')<div class="mt-1 text-sm text-rose-600">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="block text-sm text-slate-700 mb-1">Profil</label>
      <select name="profile_id" required class="w-full px-3 py-2 rounded-md border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500">
        <option value="">– Sélectionner –</option>
        @foreach($profiles as $p)
          <option value="{{ $p->id }}" @selected(old('profile_id')==$p->id)>{{ $p->nom }}</option>
        @endforeach
      </select>
      @error('profile_id')<div class="mt-1 text-sm text-rose-600">{{ $message }}</div>@enderror
    </div>
    <div>
      <label class="block text-sm text-slate-700 mb-1">Direction</label>
      <select name="direction_id" class="w-full px-3 py-2 rounded-md border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500">
        <option value="">– Aucune –</option>
        @foreach($directions as $d)
          <option value="{{ $d->id }}" @selected(old('direction_id')==$d->id)>{{ $d->description }}</option>
        @endforeach
      </select>
      @error('direction_id')<div class="mt-1 text-sm text-rose-600">{{ $message }}</div>@enderror
    </div>
    <div class="md:col-span-2 flex gap-3 mt-2">
      <button type="submit" class="px-4 py-2 rounded-md bg-sky-600 text-white hover:bg-sky-700">Créer</button>
      <a href="{{ route('admin.users') }}" class="px-4 py-2 rounded-md bg-slate-200 text-slate-800 hover:bg-slate-300">Annuler</a>
    </div>
  </form>
</div>
@endsection
