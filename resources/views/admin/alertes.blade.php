@extends('admin.layout')
@section('title','Alertes')
@section('content')
<div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 mb-6">
  <h2 class="text-lg font-semibold mb-4">Filtres</h2>
  <form method="get" action="{{ route('admin.alertes') }}" class="grid md:grid-cols-4 gap-3">
    <select name="statut" class="px-3 py-2 rounded-md border border-slate-300 bg-white">
      <option value="">– Statut –</option>
      @foreach(['en_attente'=>'En attente','en_cours'=>'En cours','termine'=>'Terminés'] as $k=>$v)
        <option value="{{ $k }}" @selected(request('statut')===$k)>{{ $v }}</option>
      @endforeach
    </select>
    <select name="type_alerte_id" class="px-3 py-2 rounded-md border border-slate-300 bg-white">
      <option value="">– Type –</option>
      @foreach($types as $t)
        <option value="{{ $t->id }}" @selected((string)request('type_alerte_id')===(string)$t->id)>{{ $t->nom }}</option>
      @endforeach
    </select>
    <select name="direction_id" class="px-3 py-2 rounded-md border border-slate-300 bg-white">
      <option value="">– Direction –</option>
      @foreach($directions as $d)
        <option value="{{ $d->id }}" @selected((string)request('direction_id')===(string)$d->id)>{{ $d->description }}</option>
      @endforeach
    </select>
    <div>
      <button type="submit" class="px-4 py-2 rounded-md bg-sky-600 text-white hover:bg-sky-700">Filtrer</button>
    </div>
  </form>
  </div>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
  <h2 class="text-lg font-semibold mb-4">Alertes</h2>
  <table class="w-full text-left border-collapse">
    <thead class="bg-slate-50 text-slate-600 text-sm">
      <tr>
        <th class="px-3 py-2 border-b">Voir</th>
        <th class="px-3 py-2 border-b">#</th>
        <th class="px-3 py-2 border-b">Titre</th>
        <th class="px-3 py-2 border-b">Statut</th>
        <th class="px-3 py-2 border-b">Type</th>
        <th class="px-3 py-2 border-b">Direction</th>
        <th class="px-3 py-2 border-b">Gestionnaire</th>
        <th class="px-3 py-2 border-b">Actions</th>
      </tr>
    </thead>
    <tbody class="divide-y">
    @foreach($alertes as $a)
      <tr data-href="{{ route('admin.alertes.show',$a) }}" class="hover:bg-slate-50 cursor-pointer">
        <td class="px-3 py-2">
          <a href="{{ route('admin.alertes.show',$a) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-md hover:bg-slate-200" title="Voir le détail">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.644C3.423 7.51 7.36 4.5 12 4.5c4.64 0 8.577 3.01 9.964 7.178.07.207.07.437 0 .644C20.577 16.49 16.64 19.5 12 19.5c-4.64 0-8.577-3.01-9.964-7.178Z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            </svg>
          </a>
        </td>
        <td class="px-3 py-2">{{ $a->id }}</td>
        <td class="px-3 py-2">{{ $a->titre }}</td>
        <td class="px-3 py-2">
          @php($c = match($a->statut){'en_attente'=>'bg-amber-100 text-amber-800','en_cours'=>'bg-blue-100 text-blue-800','termine'=>'bg-emerald-100 text-emerald-800'})
          <span class="px-2 py-1 text-xs font-medium rounded {{ $c }}">{{ $a->statut }}</span>
        </td>
        <td class="px-3 py-2">{{ optional($a->type)->nom }}</td>
        <td class="px-3 py-2">{{ optional($a->direction)->description }}</td>
        <td class="px-3 py-2">{{ optional($a->gestionnaire)->name }}</td>
        <td class="px-3 py-2">
          <form class="inline-flex flex-wrap gap-2 items-center" method="post" action="{{ route('admin.alertes.update',$a) }}">
            @csrf
            @method('put')
            <select name="statut" class="px-2 py-1 rounded-md border border-slate-300 bg-white">
              @foreach(['en_attente'=>'En attente','en_cours'=>'En cours','termine'=>'Terminés'] as $k=>$v)
                <option value="{{ $k }}" @selected($a->statut===$k)>{{ $v }}</option>
              @endforeach
            </select>
            <select name="direction_id" class="px-2 py-1 rounded-md border border-slate-300 bg-white">
              <option value="">– Direction –</option>
              @foreach($directions as $d)
                <option value="{{ $d->id }}" @selected((string)$a->direction_id===(string)$d->id)>{{ $d->description }}</option>
              @endforeach
            </select>
            <select name="gestionnaire_id" class="px-2 py-1 rounded-md border border-slate-300 bg-white">
              <option value="">– Gestionnaire –</option>
              @foreach($gestionnaires as $g)
                <option value="{{ $g->id }}" @selected((string)$a->gestionnaire_id===(string)$g->id)>{{ $g->name }}</option>
              @endforeach
            </select>
            <button type="submit" class="px-3 py-1 rounded-md bg-slate-800 text-white hover:bg-slate-900">Mettre à jour</button>
          </form>
        </td>
      </tr>
    @endforeach
    </tbody>
  </table>
  <div class="mt-4">{{ $alertes->links() }}</div>
</div>
<script>
  document.querySelectorAll('tr[data-href]').forEach(function (tr) {
    tr.addEventListener('click', function (e) {
      if (e.target.closest('a,button,select,input,label,form,svg,path')) return;
      window.location = tr.dataset.href;
    });
  });
</script>
@endsection
