<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organizaciones', function (Blueprint $table) {
            if (!Schema::hasColumn('organizaciones', 'motivo_rechazo')) {
                $table->text('motivo_rechazo')->nullable()->after('estado_revision');
            }
        });
    }

    public function down(): void
    {
        Schema::table('organizaciones', function (Blueprint $table) {
            if (Schema::hasColumn('organizaciones', 'motivo_rechazo')) {
                $table->dropColumn('motivo_rechazo');
            }
        });
    }
};