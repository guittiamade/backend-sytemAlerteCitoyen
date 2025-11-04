@extends('admin.layout')
@section('title','Notifications')
@section('content')
<div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 mb-6">
  <h2 class="text-lg font-semibold mb-4">Créer une notification</h2>
  <form method="post" action="{{ route('admin.notifications.store') }}" class="grid md:grid-cols-4 gap-3">
    @csrf
    <textarea name="message" placeholder="Message" required class="md:col-span-2 px-3 py-2 rounded-md border border-slate-300 focus:outline-none focus:ring-2 focus:ring-sky-500"></textarea>
    <select name="utilisateur_id" required class="px-3 py-2 rounded-md border border-slate-300 bg-white">
      <option value="">– Utilisateur –</option>
      @foreach($users as $u)
        <option value="{{ $u->id }}">{{ $u->name }}</option>
      @endforeach
    </select>
    <select name="alerte_id" class="px-3 py-2 rounded-md border border-slate-300 bg-white">
      <option value="">– Alerte (optionnel) –</option>
      @foreach($alertes as $a)
        <option value="{{ $a->id }}">#{{ $a->id }} – {{ $a->titre }}</option>
      @endforeach
    </select>
    <label class="inline-flex items-center gap-2 md:col-span-4">
      <input type="checkbox" name="envoyer" value="1" class="rounded border-slate-300"> Envoyer maintenant
    </label>
    <div class="md:col-span-4">
      <button type="submit" class="px-4 py-2 rounded-md bg-sky-600 text-white hover:bg-sky-700">Créer</button>
    </div>
  </form>
</div>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
  <h2 class="text-lg font-semibold mb-4">Notifications</h2>
  <table class="w-full text-left border-collapse">
    <thead class="bg-slate-50 text-slate-600 text-sm">
      <tr>
        <th class="px-3 py-2 border-b">#</th>
        <th class="px-3 py-2 border-b">Utilisateur</th>
        <th class="px-3 py-2 border-b">Alerte</th>
        <th class="px-3 py-2 border-b">Message</th>
        <th class="px-3 py-2 border-b">Statut</th>
        <th class="px-3 py-2 border-b">Date envoi</th>
        <th class="px-3 py-2 border-b">Actions</th>
      </tr>
    </thead>
    <tbody class="divide-y">
    @foreach($notifications as $n)
      <tr>
        <td class="px-3 py-2">{{ $n->id }}</td>
        <td class="px-3 py-2">{{ optional($n->utilisateur)->name }}</td>
        <td class="px-3 py-2">{{ optional($n->alerte)->id ? ('#'.$n->alerte->id) : '-' }}</td>
        <td class="px-3 py-2">{{ Str::limit($n->message, 80) }}</td>
        <td class="px-3 py-2">
          @if($n->statut)
            <span class="px-2 py-1 text-xs font-medium rounded bg-emerald-100 text-emerald-800">Envoyée</span>
          @else
            <span class="px-2 py-1 text-xs font-medium rounded bg-slate-100 text-slate-800">Brouillon</span>
          @endif
        </td>
        <td class="px-3 py-2">{{ $n->date_envoi ?? '-' }}</td>
        <td class="px-3 py-2">
          <form class="inline-flex gap-2 items-center" method="post" action="{{ route('admin.notifications.update',$n) }}">
            @csrf
            @method('put')
            <input type="text" name="message" value="{{ $n->message }}" class="px-2 py-1 rounded-md border border-slate-300 w-64">
            <label class="inline-flex items-center gap-2"><input type="checkbox" name="statut" value="1" @checked($n->statut) class="rounded border-slate-300"> Envoyée</label>
            <button type="submit" class="px-3 py-1 rounded-md bg-slate-800 text-white hover:bg-slate-900">Mettre à jour</button>
          </form>
          <form class="inline" method="post" action="{{ route('admin.notifications.destroy',$n) }}" onsubmit="return confirm('Supprimer ?')">
            @csrf
            @method('delete')
            <button type="submit" class="ml-2 px-3 py-1 rounded-md bg-rose-600 text-white hover:bg-rose-700">Supprimer</button>
          </form>
        </td>
      </tr>
    @endforeach
    </tbody>
  </table>
  <div class="mt-4">{{ $notifications->links() }}</div>
</div>
@endsection
