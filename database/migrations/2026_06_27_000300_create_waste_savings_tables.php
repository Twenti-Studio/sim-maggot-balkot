<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waste_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('unit')->default('kg');
            $table->decimal('price_per_unit', 15, 2);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });

        Schema::create('waste_deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('location_id')->constrained()->restrictOnDelete();
            $table->foreignId('waste_type_id')->constrained()->restrictOnDelete();
            $table->date('deposit_date')->index();
            $table->decimal('weight', 12, 2);
            $table->decimal('price_per_unit', 15, 2);
            $table->decimal('total_amount', 15, 2);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->index(['user_id', 'deposit_date']);
            $table->index(['location_id', 'deposit_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waste_deposits');
        Schema::dropIfExists('waste_types');
    }
};
