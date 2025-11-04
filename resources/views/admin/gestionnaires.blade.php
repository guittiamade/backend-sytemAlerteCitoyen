@extends('admin.layout')
@section('title','Gestionnaires')
@section('content')
<div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 mb-6 flex items-center justify-between">
  <h2 class="text-lg font-semibold">Gestionnaires</h2>
  <a href="{{ route('admin.gestionnaires.create') }}" class="px-4 py-2 rounded-md bg-sky-600 text-white hover:bg-sky-700">Créer un gestionnaire</a>
</div>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
  <h2 class="text-lg font-semibold mb-4">Liste des gestionnaires</h2>
  <table class="w-full text-left border-collapse">
    <thead class="bg-slate-50 text-slate-600 text-sm">
      <tr>
        <th class="px-3 py-2 border-b">Nom</th>
        <th class="px-3 py-2 border-b">Email</th>
        <th class="px-3 py-2 border-b">Téléphone</th>
        <th class="px-3 py-2 border-b">Direction</th>
        <th class="px-3 py-2 border-b">Profil</th>
        <th class="px-3 py-2 border-b">Éditer</th>
        <th class="px-3 py-2 border-b">Supprimer</th>
      </tr>
    </thead>
    <tbody class="divide-y">
    @foreach($users as $u)
      <tr>
        <td class="px-3 py-2">{{ $u->name }}</td>
        <td class="px-3 py-2">{{ $u->email }}</td>
        <td class="px-3 py-2">{{ $u->tel }}</td>
        <td class="px-3 py-2">{{ optional($u->direction)->description }}</td>
        <td class="px-3 py-2">{{ optional($u->profile)->nom }}</td>
        <td class="px-3 py-2">
          <a href="{{ route('admin.users.edit',$u) }}" class="px-3 py-1 rounded-md bg-sky-600 text-white hover:bg-sky-700">Éditer</a>
        </td>
        <td class="px-3 py-2">
          <form class="inline" method="post" action="{{ route('admin.users.destroy',$u) }}" onsubmit="return confirm('Supprimer ?')">
            @csrf
            @method('delete')
            <button type="submit" class="px-3 py-1 rounded-md bg-rose-600 text-white hover:bg-rose-700">Supprimer</button>
          </form>
        </td>
      </tr>
    @endforeach
    </tbody>
  </table>
  <div class="mt-4">{{ $users->links() }}</div>
</div>
@endsection
