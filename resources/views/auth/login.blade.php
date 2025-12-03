<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Connexion – Back Office</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>html{font-family:Inter,system-ui,Arial,sans-serif}</style>
</head>
<body class="min-h-screen bg-slate-950 text-slate-900">
  <div class="absolute inset-0 bg-gradient-to-br from-sky-600 via-blue-600 to-indigo-700 opacity-90"></div>
  <div class="relative min-h-screen flex flex-col lg:flex-row">
    <section class="flex-1 hidden lg:flex flex-col justify-between text-white p-12">
      <div>
        <div class="inline-flex items-center gap-3 px-4 py-2 rounded-full border border-white/30 text-sm uppercase tracking-[0.2em]">Alerte Citoyen</div>
        <h1 class="text-4xl font-semibold mt-8 leading-tight">Back Office <span class="text-white/80 font-light">professionnel</span></h1>
        <p class="mt-4 text-white/80 text-lg max-w-lg">Suivez les signalements, coordonnez les directions et accédez au tableau de bord modernisé en un seul endroit.</p>
      </div>
      <div class="grid grid-cols-2 gap-4 mt-16 max-w-lg">
        <div class="rounded-2xl bg-white/10 backdrop-blur px-4 py-5">
          <p class="text-xs uppercase tracking-widest text-white/60">Alertes résolues</p>
          <p class="text-3xl font-semibold">{{ number_format(320) }}+</p>
        </div>
        <div class="rounded-2xl bg-white/10 backdrop-blur px-4 py-5">
          <p class="text-xs uppercase tracking-widest text-white/60">Directions actives</p>
          <p class="text-3xl font-semibold">{{ number_format(12) }}</p>
        </div>
      </div>
    </section>
    <section class="flex-1 bg-white rounded-t-[40px] lg:rounded-l-[40px] lg:rounded-t-none p-6 sm:p-10 flex items-center justify-center shadow-2xl">
      <div class="w-full max-w-md">
        <div class="mb-8 text-center">
          <p class="text-sm uppercase tracking-[0.3em] text-slate-400">Administration</p>
          <h2 class="text-3xl font-semibold text-slate-900 mt-2">Connexion sécurisée</h2>
          <p class="text-slate-500 mt-2">Utilisez vos identifiants super administrateur pour accéder au tableau de bord.</p>
        </div>
        <form method="post" action="{{ route('login.post') }}" class="space-y-4">
          @csrf
          @if($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50 text-rose-700 px-4 py-3">{{ $errors->first() }}</div>
          @endif
          <div>
            <label for="email" class="block text-sm font-semibold text-slate-700 mb-1">Email professionnel</label>
            <div class="relative">
              <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l9 6 9-6m-18 8h18V8H3v8z"/></svg>
              </span>
              <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-11 py-3 focus:border-sky-500 focus:bg-white focus:ring-2 focus:ring-sky-200" placeholder="admin@commune.fr" />
            </div>
          </div>
          <div>
            <label for="password" class="block text-sm font-semibold text-slate-700 mb-1">Mot de passe</label>
            <div class="relative">
              <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6a4.5 4.5 0 1 0-9 0v4.5m9 0H6a1.5 1.5 0 0 0-1.5 1.5v6A1.5 1.5 0 0 0 6 19.5h10.5a1.5 1.5 0 0 0 1.5-1.5v-6a1.5 1.5 0 0 0-1.5-1.5z"/></svg>
              </span>
              <input id="password" type="password" name="password" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-11 py-3 focus:border-sky-500 focus:bg-white focus:ring-2 focus:ring-sky-200" placeholder="••••••••" />
            </div>
          </div>
          <div class="flex flex-wrap items-center justify-between text-sm text-slate-500">
            <label class="inline-flex items-center gap-2">
              <input type="checkbox" name="remember" class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
              Se souvenir de moi
            </label>
            <a href="#" class="font-semibold text-sky-600 hover:text-sky-500">Mot de passe oublié ?</a>
          </div>
          <button class="w-full rounded-2xl bg-gradient-to-r from-sky-500 to-indigo-500 text-white font-semibold py-3 shadow-lg shadow-sky-200 hover:translate-y-[1px] transition" type="submit">Se connecter</button>
          <div class="rounded-2xl bg-slate-50 border border-slate-100 text-slate-500 text-sm px-4 py-3">
            Astuce: utilisez <code>admin@demo.local</code> / <code>password</code> pour l’environnement seed.
          </div>
        </form>
      </div>
    </section>
  </div>
</body>
</html>


