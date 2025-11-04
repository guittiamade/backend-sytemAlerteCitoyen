<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('tel')->nullable()->after('email');
            $table->foreignId('profile_id')->nullable()->constrained('profiles')->nullOnDelete()->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('profile_id');
            $table->dropColumn(['tel']);
        });
    }
};


