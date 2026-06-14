<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('qr_codes', function (Blueprint $table) {
            $table->string('finder_color')->default('#000000')->after('logo_path');
            $table->string('data_color')->default('#000000')->after('finder_color');
            $table->string('bg_color')->default('#FFFFFF')->after('data_color');
        });
    }

    public function down(): void
    {
        Schema::table('qr_codes', function (Blueprint $table) {
            $table->dropColumn(['finder_color', 'data_color', 'bg_color']);
        });
    }
};
