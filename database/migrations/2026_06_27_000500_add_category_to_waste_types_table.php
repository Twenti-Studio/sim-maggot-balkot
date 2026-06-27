<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('waste_types', function (Blueprint $table) {
            $table->string('category')->default('Non-organik')->after('name')->index();
        });

        DB::table('waste_types')
            ->whereIn('name', ['Botol plastik', 'Kardus'])
            ->update(['category' => 'Daur ulang']);
    }

    public function down(): void
    {
        Schema::table('waste_types', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
