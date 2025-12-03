@extends('admin.layout')
@section('title','Créer un type d’alerte')
@section('content')
<div class="max-w-2xl bg-white rounded-xl border border-slate-200 shadow-sm p-6">
  <h1 class="text-xl font-semibold mb-4">Créer un type d’alerte</h1>
  <form method="post" action="{{ route('admin.types.store') }}" class="grid md:grid-cols-2 gap-4">
    @csrf
    <div class="md:col-span-2">
      <label class="block text-sm text-slate-700 mb-1">Nom</label>
      <input name="nom" value="{{ old('nom') }}" required class="w-full px-3 py-2 rounded-md border border-slate-300 focus:outline-none focus:ring-2 focus:ring-sky-500">
      @error('nom')<div class="mt-1 text-sm text-rose-600">{{ $message }}</div>@enderror
    </div>
    <div class="md:col-span-2">
      <label class="block text-sm text-slate-700 mb-1">Description</label>
      <input name="description" value="{{ old('description') }}" class="w-full px-3 py-2 rounded-md border border-slate-300 focus:outline-none focus:ring-2 focus:ring-sky-500">
      @error('description')<div class="mt-1 text-sm text-rose-600">{{ $message }}</div>@enderror
    </div>
    <div class="md:col-span-2">
      <label class="block text-sm text-slate-700 mb-1">Image (URL)</label>
      <input name="image" type="url" value="{{ old('image') }}" class="w-full px-3 py-2 rounded-md border border-slate-300 focus:outline-none focus:ring-2 focus:ring-sky-500" placeholder="https://exemple.com/types/voirie.png">
      @error('image')<div class="mt-1 text-sm text-rose-600">{{ $message }}</div>@enderror
    </div>
    <div class="md:col-span-2 flex gap-3 mt-2">
      <button type="submit" class="px-4 py-2 rounded-md bg-sky-600 text-white hover:bg-sky-700">Créer</button>
      <a href="{{ route('admin.types') }}" class="px-4 py-2 rounded-md bg-slate-200 text-slate-800 hover:bg-slate-300">Annuler</a>
    </div>
  </form>
</div>
@endsection
