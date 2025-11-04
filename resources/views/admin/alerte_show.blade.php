@extends('admin.layout')
@section('title','Détail alerte')
@section('content')
<div class="flex items-center justify-between mb-4">
  <h1 class="text-2xl font-semibold">Alerte #{{ $alerte->id }}</h1>
  <a href="{{ route('admin.alertes') }}" class="px-3 py-2 rounded-md bg-slate-200 text-slate-800 hover:bg-slate-300">Retour</a>
</div>

<div class="grid md:grid-cols-2 gap-4">
  <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
    <h2 class="text-lg font-semibold mb-3">Informations</h2>
    <dl class="divide-y">
      <div class="py-2 flex justify-between"><dt class="text-slate-600">Titre</dt><dd class="font-medium text-right">{{ $alerte->titre }}</dd></div>
      <div class="py-2"><div class="text-slate-600 mb-1">Description</div><div>{{ $alerte->description }}</div></div>
      <div class="py-2 flex justify-between"><dt class="text-slate-600">Localisation</dt><dd class="font-medium">{{ $alerte->localisation ?? '-' }}</dd></div>
      <div class="py-2 flex justify-between"><dt class="text-slate-600">Type</dt><dd class="font-medium">{{ optional($alerte->type)->nom }}</dd></div>
      <div class="py-2 flex justify-between"><dt class="text-slate-600">Statut</dt><dd class="font-medium">{{ $alerte->statut }}</dd></div>
      <div class="py-2 flex justify-between"><dt class="text-slate-600">Citoyen</dt><dd class="font-medium">{{ optional($alerte->citoyen)->name }}</dd></div>
      <div class="py-2 flex justify-between"><dt class="text-slate-600">Gestionnaire</dt><dd class="font-medium">{{ optional($alerte->gestionnaire)->name ?? '-' }}</dd></div>
      <div class="py-2 flex justify-between"><dt class="text-slate-600">Direction</dt><dd class="font-medium">{{ optional($alerte->direction)->description ?? '-' }}</dd></div>
      <div class="py-2 flex justify-between"><dt class="text-slate-600">Créée le</dt><dd class="font-medium">{{ $alerte->created_at }}</dd></div>
    </dl>
  </div>

  <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
    <h2 class="text-lg font-semibold mb-3">Actions rapides</h2>
    <form method="post" action="{{ route('admin.alertes.update',$alerte) }}" class="grid md:grid-cols-2 gap-3">
      @csrf
      @method('put')
      <select name="statut" class="px-3 py-2 rounded-md border border-slate-300 bg-white">
        @foreach(['en_attente'=>'En attente','en_cours'=>'En cours','termine'=>'Terminés'] as $k=>$v)
          <option value="{{ $k }}" @selected($alerte->statut===$k)>{{ $v }}</option>
        @endforeach
      </select>
      <select name="direction_id" class="px-3 py-2 rounded-md border border-slate-300 bg-white">
        <option value="">– Direction –</option>
        @foreach($directions as $d)
          <option value="{{ $d->id }}" @selected((string)$alerte->direction_id===(string)$d->id)>{{ $d->description }}</option>
        @endforeach
      </select>
      <select name="gestionnaire_id" class="px-3 py-2 rounded-md border border-slate-300 bg-white">
        <option value="">– Gestionnaire –</option>
        @foreach($gestionnaires as $g)
          <option value="{{ $g->id }}" @selected((string)$alerte->gestionnaire_id===(string)$g->id)>{{ $g->name }}</option>
        @endforeach
      </select>
      <div class="md:col-span-2">
        <button type="submit" class="px-4 py-2 rounded-md bg-sky-600 text-white hover:bg-sky-700">Enregistrer</button>
      </div>
    </form>
  </div>
</div>
@endsection
