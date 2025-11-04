<?php

namespace App\Http\Controllers;

use App\Models\Alerte;
use Illuminate\Http\Request;

class AdminExportController extends Controller
{
    public function alertesCsv(Request $request)
    {
        $query = Alerte::with(['type', 'direction']);
        if ($s = $request->query('statut')) {
            $query->where('statut', $s);
        }
        $rows = $query->orderByDesc('id')->get([
            'id','titre','statut','localisation','type_alerte_id','direction_id','created_at','updated_at'
        ]);

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="alertes.csv"',
        ];

        $callback = static function () use ($rows): void {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['id','titre','statut','type','direction','localisation','created_at','updated_at']);
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->id,
                    $r->titre,
                    $r->statut,
                    optional($r->type)->nom,
                    optional($r->direction)->description,
                    $r->localisation,
                    $r->created_at,
                    $r->updated_at,
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}


