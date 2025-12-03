<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Backfill missing phone numbers to avoid violating the upcoming NOT NULL + UNIQUE constraint.
        DB::table('users')
            ->whereNull('tel')
            ->chunkById(100, function ($users): void {
                foreach ($users as $user) {
                    DB::table('users')
                        ->where('id', $user->id)
                        ->update([
                            'tel' => sprintf('070000%05d', $user->id),
                        ]);
                }
            }, 'id');

        Schema::table('users', function (Blueprint $table): void {
            $table->string('email')->nullable()->change();
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->string('tel')->nullable(false)->change();
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->unique('tel');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropUnique('users_tel_unique');
        });

        // Ensure all users have an email again before making it required.
        DB::table('users')
            ->whereNull('email')
            ->chunkById(100, function ($users): void {
                foreach ($users as $user) {
                    DB::table('users')
                        ->where('id', $user->id)
                        ->update([
                            'email' => sprintf('restored-%s@example.com', $user->id),
                        ]);
                }
            }, 'id');

        Schema::table('users', function (Blueprint $table): void {
            $table->string('tel')->nullable()->change();
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->string('email')->nullable(false)->change();
        });
    }
};

