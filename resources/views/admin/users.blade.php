@extends('admin.layout')
@section('title','Utilisateurs')
@section('content')
<div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 mb-6 flex items-center justify-between">
  <h2 class="text-lg font-semibold">Utilisateurs</h2>
  <a href="{{ route('admin.users.create') }}" class="px-4 py-2 rounded-md bg-sky-600 text-white hover:bg-sky-700">Créer un utilisateur</a>
  </div>
<div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
  <h2 class="text-lg font-semibold mb-4">Utilisateurs</h2>
  <table class="w-full text-left border-collapse">
    <thead class="bg-slate-50 text-slate-600 text-sm">
      <tr>
        <th class="px-3 py-2 border-b">Nom</th>
        <th class="px-3 py-2 border-b">Email</th>
        <th class="px-3 py-2 border-b">Téléphone</th>
        <th class="px-3 py-2 border-b">Profil</th>
        <th class="px-3 py-2 border-b">Actions</th>
        <th class="px-3 py-2 border-b">Éditer</th>
      </tr>
    </thead>
    <tbody class="divide-y">
    @foreach($users as $u)
      <tr>
        <td class="px-3 py-2">{{ $u->name }}</td>
        <td class="px-3 py-2">{{ $u->email }}</td>
        <td class="px-3 py-2">{{ $u->tel }}</td>
        <td class="px-3 py-2">
          <form class="inline" method="post" action="{{ route('admin.users.update',$u) }}">
            @csrf
            @method('put')
            <select name="profile_id" class="px-2 py-1 rounded-md border border-slate-300 bg-white">
              @foreach($profiles as $p)
                <option value="{{ $p->id }}" @selected($u->profile_id==$p->id)>{{ $p->nom }}</option>
              @endforeach
            </select>
            <button type="submit" class="ml-2 px-3 py-1 rounded-md bg-slate-800 text-white hover:bg-slate-900">Changer</button>
          </form>
        </td>
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


