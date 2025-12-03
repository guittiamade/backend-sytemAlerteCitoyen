@extends('admin.layout')
@section('title','Dashboard')
@section('content')
    <div class="mb-6 rounded-3xl bg-gradient-to-r from-sky-600 via-sky-500 to-indigo-500 text-white p-6 shadow-lg flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div>
            <p class="text-white/70 text-sm uppercase tracking-widest">Bienvenue sur l’espace Admin</p>
            <h1 class="text-3xl font-semibold mt-2">Suivi des alertes citoyennes</h1>
            <p class="mt-2 text-white/80">Visualisez en un coup d’œil la performance de vos équipes, l’activité quotidienne et les points clés à surveiller.</p>
        </div>
        <div class="mt-4 lg:mt-0 flex gap-3">
            <a href="{{ route('admin.alertes') }}" class="px-4 py-2 rounded-xl bg-white text-sky-600 font-semibold shadow hover:bg-slate-100 transition">Voir les alertes</a>
            <a href="{{ route('admin.export.alertes') }}" class="px-4 py-2 rounded-xl border border-white/60 text-white font-semibold hover:bg-white/10 transition">Exporter</a>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4 mb-8">
        @php
            $cards = [
                [
                    'label' => 'Signalements total',
                    'value' => $stats['alertes_total'],
                    'icon' => 'bell',
                    'accent' => 'from-sky-500 to-indigo-500',
                    'badge' => ($stats['alertes_total'] ? round(($stats['alertes_termine'] / max($stats['alertes_total'],1))*100) : 0) . '% résolus'
                ],
                [
                    'label' => 'En attente',
                    'value' => $stats['alertes_en_attente'],
                    'icon' => 'clock',
                    'accent' => 'from-amber-500 to-orange-500',
                    'badge' => 'À traiter'
                ],
                [
                    'label' => 'En cours',
                    'value' => $stats['alertes_en_cours'],
                    'icon' => 'arrows',
                    'accent' => 'from-sky-500 to-blue-500',
                    'badge' => 'Suivi actif'
                ],
                [
                    'label' => 'Terminées',
                    'value' => $stats['alertes_termine'],
                    'icon' => 'check',
                    'accent' => 'from-emerald-500 to-lime-500',
                    'badge' => 'Clôturées'
                ],
            ];
            $icons = [
                'bell' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9a6 6 0 0 0-12 0v.75a8.967 8.967 0 0 1-2.311 6.022c1.733.64 3.56 1.085 5.455 1.31m5.713 0a24.255 24.255 0 0 1-5.713 0m5.713 0a3 3 0 1 1-5.713 0" /></svg>',
                'clock' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>',
                'arrows' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 9h15M4.5 15h8.25"/></svg>',
                'check' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>',
            ];
        @endphp
        @foreach($cards as $card)
            <div class="rounded-3xl border border-slate-100 bg-white p-5 shadow-sm hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-slate-500">{{ $card['label'] }}</p>
                    <span class="inline-flex items-center justify-center rounded-full bg-gradient-to-r {{ $card['accent'] }} text-white w-10 h-10">
                        {!! $icons[$card['icon']] !!}
                    </span>
                </div>
                <div class="mt-3 text-3xl font-semibold text-slate-900">{{ $card['value'] }}</div>
                <p class="mt-2 text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $card['badge'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 bg-white rounded-3xl shadow border border-slate-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm text-slate-500">Évolution quotidienne</p>
                    <h2 class="text-xl font-semibold text-slate-900">Alertes reçues sur 7 jours</h2>
                </div>
                <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-sm text-slate-600">
                    <span class="h-2 w-2 rounded-full bg-sky-500"></span> Derniers jours
                </span>
            </div>
            <canvas id="lineChart" height="140"></canvas>
        </div>
        <div class="bg-white rounded-3xl shadow border border-slate-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-slate-900">Répartition des statuts</h2>
                <span class="text-sm text-slate-500">{{ $stats['alertes_total'] }} alertes totales</span>
            </div>
            <div class="flex items-center gap-4">
                <div class="w-40">
                    <canvas id="statusChart" height="160"></canvas>
                </div>
                <div class="flex-1 space-y-4">
                    @foreach($statusBreakdown as $label => $value)
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-slate-700">{{ $label }}</p>
                                <p class="text-xs text-slate-400"> {{ $stats['alertes_total'] ? round(($value / max($stats['alertes_total'],1))*100) : 0 }}%</p>
                            </div>
                            <div class="text-lg font-semibold text-slate-900">{{ $value }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3 mt-6">
        <div class="lg:col-span-2 bg-white rounded-3xl shadow border border-slate-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-slate-900">Alertes récentes</h2>
                <a href="{{ route('admin.alertes') }}" class="text-sky-600 text-sm font-semibold hover:underline">Voir toutes</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="text-slate-500 text-xs uppercase tracking-wide">
                        <tr>
                            <th class="py-3">Titre</th>
                            <th class="py-3">Type</th>
                            <th class="py-3">Direction</th>
                            <th class="py-3">Statut</th>
                            <th class="py-3 text-right">Créée le</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentAlertes as $alerte)
                            <tr class="hover:bg-slate-50/70">
                                <td class="py-3 font-medium text-slate-900">{{ $alerte->titre }}</td>
                                <td class="py-3 text-slate-600">{{ optional($alerte->type)->nom ?? '—' }}</td>
                                <td class="py-3 text-slate-600">{{ optional($alerte->direction)->description ?? '—' }}</td>
                                <td class="py-3">
                                    @php
                                        $colors = [
                                            'en_attente' => 'bg-amber-100 text-amber-700',
                                            'en_cours' => 'bg-sky-100 text-sky-700',
                                            'termine' => 'bg-emerald-100 text-emerald-700',
                                        ];
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $colors[$alerte->statut] ?? 'bg-slate-100 text-slate-700' }}">
                                        {{ str_replace('_',' ', ucfirst($alerte->statut)) }}
                                    </span>
                                </td>
                                <td class="py-3 text-right text-slate-500">{{ $alerte->created_at?->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-slate-500">Aucune alerte récente pour le moment.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="bg-white rounded-3xl shadow border border-slate-100 p-6 space-y-6">
            <div>
                <h2 class="text-xl font-semibold text-slate-900">Types les plus sollicités</h2>
                <p class="text-sm text-slate-500">Top 4 des catégories d’alertes</p>
            </div>
            <div class="space-y-4">
                @forelse($topTypes as $type)
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-slate-900">{{ $type->nom }}</p>
                            <p class="text-xs text-slate-400">{{ $type->alertes_count }} alertes</p>
                        </div>
                        <div class="flex -space-x-2">
                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-slate-100 text-slate-700 text-sm font-semibold">{{ strtoupper(substr($type->nom,0,2)) }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Aucun type enregistré.</p>
                @endforelse
            </div>
            <div class="rounded-2xl bg-slate-50 p-4 text-sm text-slate-600">
                <p class="font-semibold text-slate-900 mb-1">Astuce</p>
                Ajoutez une image aux types d’alertes pour aider les gestionnaires à les identifier plus rapidement dans les interfaces.
        </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const lineCtx = document.getElementById('lineChart');
        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: @json($trend->pluck('label')),
                datasets: [{
                    data: @json($trend->pluck('value')),
                    borderColor: '#0ea5e9',
                    backgroundColor: 'rgba(14,165,233,.15)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 5,
                    pointBackgroundColor: '#0ea5e9',
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                    x: { grid: { display: false } }
                }
            }
        });

        const statusCtx = document.getElementById('statusChart');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: @json(array_keys($statusBreakdown)),
                datasets: [{
                    data: @json(array_values($statusBreakdown)),
                    backgroundColor: ['#fcd34d', '#0ea5e9', '#34d399'],
                    borderWidth: 0
                }]
            },
            options: {
                cutout: '70%',
                plugins: {
                    legend: { display: false }
                }
            }
        });
    </script>
@endsection


