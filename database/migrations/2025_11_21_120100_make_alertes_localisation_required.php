<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('alertes')
            ->whereNull('localisation')
            ->update(['localisation' => 'Localisation non fournie']);

        Schema::table('alertes', function (Blueprint $table): void {
            $table->string('localisation')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('alertes', function (Blueprint $table): void {
            $table->string('localisation')->nullable()->change();
        });
    }
};

