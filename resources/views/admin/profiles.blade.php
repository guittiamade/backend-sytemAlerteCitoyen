@extends('admin.layout')
@section('title','Profils')
@section('content')
<div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 mb-6">
  <form method="get" action="{{ route('admin.profiles') }}" class="flex gap-2">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher un profil..." class="flex-1 px-3 py-2 rounded-md border border-slate-300 focus:outline-none focus:ring-2 focus:ring-sky-500">
    <button class="px-4 py-2 rounded-md bg-slate-200 text-slate-800 hover:bg-slate-300" type="submit">Rechercher</button>
    <a href="{{ route('admin.profiles.create') }}" class="px-4 py-2 rounded-md bg-sky-600 text-white hover:bg-sky-700">Créer un profil</a>
  </form>
</div>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
  <h2 class="text-lg font-semibold mb-4">Profils</h2>
  <table class="w-full text-left border-collapse">
    <thead class="bg-slate-50 text-slate-600 text-sm">
      <tr>
        <th class="px-3 py-2 border-b">Nom</th>
        <th class="px-3 py-2 border-b">Description</th>
        <th class="px-3 py-2 border-b">Éditer</th>
        <th class="px-3 py-2 border-b">Supprimer</th>
      </tr>
    </thead>
    <tbody class="divide-y">
    @foreach($profiles as $p)
      <tr data-href="{{ route('admin.profiles.edit',$p) }}" class="hover:bg-slate-50 cursor-pointer">
        <td class="px-3 py-2">{{ $p->nom }}</td>
        <td class="px-3 py-2">{{ $p->description }}</td>
        <td class="px-3 py-2">
          <a href="{{ route('admin.profiles.edit',$p) }}" class="px-3 py-1 rounded-md bg-sky-600 text-white hover:bg-sky-700">Éditer</a>
        </td>
        <td class="px-3 py-2">
          <form class="inline" method="post" action="{{ route('admin.profiles.destroy',$p) }}" onsubmit="return confirm('Supprimer ?')">
            @csrf
            @method('delete')
            <button type="submit" class="px-3 py-1 rounded-md bg-rose-600 text-white hover:bg-rose-700">Supprimer</button>
          </form>
        </td>
      </tr>
    @endforeach
    </tbody>
  </table>
  <div class="mt-4">{{ $profiles->links() }}</div>
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
