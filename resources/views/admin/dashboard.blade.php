@extends('admin.layout')
@section('title','Dashboard')
@section('content')
    <h1 class="text-2xl font-semibold mb-4">Tableau de bord</h1>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
            <div class="text-slate-600">Signalements</div>
            <div class="text-3xl font-bold mt-1">{{ $stats['alertes_total'] }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
            <div class="text-slate-600">En attente</div>
            <div class="text-3xl font-bold mt-1">{{ $stats['alertes_en_attente'] }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
            <div class="text-slate-600">En cours</div>
            <div class="text-3xl font-bold mt-1">{{ $stats['alertes_en_cours'] }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
            <div class="text-slate-600">Terminés</div>
            <div class="text-3xl font-bold mt-1">{{ $stats['alertes_termine'] }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
            <div class="text-slate-600">Directions</div>
            <div class="text-3xl font-bold mt-1">{{ $stats['directions'] }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
            <div class="text-slate-600">Types</div>
            <div class="text-3xl font-bold mt-1">{{ $stats['types'] }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
            <div class="text-slate-600">Utilisateurs</div>
            <div class="text-3xl font-bold mt-1">{{ $stats['utilisateurs'] }}</div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 mt-6">
        <h2 class="text-lg font-semibold mb-4">Evolution des statuts</h2>
        <canvas id="chart" height="100"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('chart');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['En attente', 'En cours', 'Terminés'],
                datasets: [{
                    label: 'Signalements',
                    data: [{{ $stats['alertes_en_attente'] }}, {{ $stats['alertes_en_cours'] }}, {{ $stats['alertes_termine'] }}],
                    backgroundColor: ['#fde68a','#93c5fd','#86efac']
                }]
            },
            options: {responsive: true, plugins: {legend: {display: false}}}
        });
    </script>
@endsection


