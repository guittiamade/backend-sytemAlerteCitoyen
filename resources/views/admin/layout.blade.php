<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title','Admin')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>html{font-family:Inter,system-ui,Arial,sans-serif}</style>
    </head>
<body class="bg-slate-50 text-slate-900">
    <div class="min-h-screen grid grid-cols-[260px_1fr]">
        <aside class="bg-sky-600 text-white flex flex-col">
            <div class="px-5 py-4 border-b border-sky-500/40">
                <div class="text-lg font-semibold">Back Office</div>
                <div class="text-sky-100 text-sm">Alerte Citoyen</div>
            </div>
            <nav class="p-3 space-y-1">
                <a href="/admin" class="block px-3 py-2 rounded-md {{ request()->is('admin') ? 'bg-white/20' : 'hover:bg-white/10' }}">Dashboard</a>
                <a href="/admin/alertes" class="block px-3 py-2 rounded-md {{ request()->is('admin/alertes*') ? 'bg-white/20' : 'hover:bg-white/10' }}">Alertes</a>
                <a href="/admin/directions" class="block px-3 py-2 rounded-md {{ request()->is('admin/directions*') ? 'bg-white/20' : 'hover:bg-white/10' }}">Directions</a>
                <a href="/admin/types" class="block px-3 py-2 rounded-md {{ request()->is('admin/types*') ? 'bg-white/20' : 'hover:bg-white/10' }}">Types d’alertes</a>
                <a href="/admin/users" class="block px-3 py-2 rounded-md {{ request()->is('admin/users*') ? 'bg-white/20' : 'hover:bg-white/10' }}">Utilisateurs</a>
                <a href="/admin/gestionnaires" class="block px-3 py-2 rounded-md {{ request()->is('admin/gestionnaires*') ? 'bg-white/20' : 'hover:bg-white/10' }}">Gestionnaires</a>
                <a href="/admin/profiles" class="block px-3 py-2 rounded-md {{ request()->is('admin/profiles*') ? 'bg-white/20' : 'hover:bg-white/10' }}">Profils</a>
                <a href="/admin/export/alertes.csv" class="block px-3 py-2 rounded-md hover:bg-white/10">Exporter CSV</a>
            </nav>
            <div class="mt-auto p-3 border-t border-sky-500/40">
                <form method="post" action="{{ route('logout') }}" class="w-full">@csrf
                    <button type="submit" class="w-full px-3 py-2 rounded-md bg-white text-sky-700 font-medium hover:bg-slate-100">Se déconnecter</button>
                </form>
            </div>
        </aside>
        <main class="p-6">
            @if(session('ok'))
                <div class="mb-4 rounded-lg border border-emerald-300 bg-emerald-50 text-emerald-800 px-4 py-3">{{ session('ok') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 rounded-lg border border-rose-300 bg-rose-50 text-rose-800 px-4 py-3">{{ session('error') }}</div>
            @endif
            @yield('content')
        </main>
    </div>
</body>
</html>


