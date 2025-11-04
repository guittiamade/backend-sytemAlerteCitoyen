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
<body class="min-h-screen bg-gradient-to-br from-sky-500 to-cyan-400 grid place-items-center p-4">
  <div class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
    <div class="px-6 pt-6">
      <h1 class="text-xl font-semibold">Back Office – Connexion</h1>
      <p class="text-slate-600 mt-1 mb-4">Connectez‑vous pour accéder au tableau de bord.</p>
    </div>
    <form method="post" action="{{ route('login.post') }}" class="px-6 pb-6">
      @csrf
      @if($errors->any())
        <div class="mb-3 rounded-lg border border-rose-300 bg-rose-50 text-rose-800 px-4 py-3">{{ $errors->first() }}</div>
      @endif
      <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
      <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full mb-3 px-3 py-2 rounded-md border border-slate-300 focus:outline-none focus:ring-2 focus:ring-sky-500" />
      <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Mot de passe</label>
      <input id="password" type="password" name="password" required class="w-full mb-3 px-3 py-2 rounded-md border border-slate-300 focus:outline-none focus:ring-2 focus:ring-sky-500" />
      <div class="flex items-center justify-between mb-3 text-sm">
        <label class="inline-flex items-center gap-2"><input type="checkbox" name="remember" class="rounded border-slate-300"> Se souvenir de moi</label>
        <a href="#" class="text-sky-700 hover:underline">Mot de passe oublié ?</a>
      </div>
      <button class="w-full px-4 py-2 rounded-md bg-sky-600 text-white font-medium hover:bg-sky-700" type="submit">Se connecter</button>
    </form>
    <div class="px-6 py-3 bg-slate-50 border-t border-slate-200 text-slate-600 text-sm">Astuce: utilisez <code>admin@demo.local</code> / <code>password</code> (seed).</div>
  </div>
</body>
</html>


