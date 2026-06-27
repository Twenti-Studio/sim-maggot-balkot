<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waste_withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('location_id')->constrained()->restrictOnDelete();
            $table->date('withdrawal_date')->index();
            $table->decimal('amount', 15, 2);
            $table->string('status')->default('submitted')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'withdrawal_date']);
            $table->index(['location_id', 'withdrawal_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waste_withdrawals');
    }
};
