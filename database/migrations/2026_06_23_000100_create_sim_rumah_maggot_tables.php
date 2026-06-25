<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('operator')->index();
            $table->string('phone')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
        });

        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->foreignId('location_id')->constrained()->restrictOnDelete();
            $table->string('employee_code')->unique();
            $table->string('name');
            $table->string('position');
            $table->string('phone')->nullable();
            $table->date('joined_at')->nullable();
            $table->string('status')->default('active')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained()->restrictOnDelete();
            $table->date('report_date')->index();
            $table->decimal('organic_waste_kg', 12, 2)->default(0);
            $table->string('organic_source')->nullable();
            $table->text('organic_notes')->nullable();
            $table->decimal('non_organic_waste_kg', 12, 2)->default(0);
            $table->string('non_organic_type')->nullable();
            $table->text('non_organic_notes')->nullable();
            $table->decimal('eggs_amount', 12, 2)->default(0);
            $table->string('eggs_unit')->default('gram');
            $table->decimal('baby_maggot_kg', 12, 2)->default(0);
            $table->decimal('adult_maggot_kg', 12, 2)->default(0);
            $table->decimal('prepupa_kg', 12, 2)->default(0);
            $table->unsignedInteger('bsf_flies_count')->default(0);
            $table->decimal('kasgot_kg', 12, 2)->default(0);
            $table->decimal('other_fertilizer_kg', 12, 2)->default(0);
            $table->decimal('wet_maggot_kg', 12, 2)->default(0);
            $table->decimal('dry_maggot_kg', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->string('status')->default('draft')->index();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('revision_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['location_id', 'report_date']);
        });

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained()->cascadeOnDelete();
            $table->date('attendance_date')->index();
            $table->timestamp('check_in_at')->nullable();
            $table->timestamp('check_out_at')->nullable();
            $table->string('status')->default('present')->index();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->unique(['staff_id', 'attendance_date']);
        });

        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained()->restrictOnDelete();
            $table->string('asset_code')->unique();
            $table->string('name');
            $table->string('category')->index();
            $table->string('location_detail')->nullable();
            $table->date('purchased_at')->nullable();
            $table->decimal('purchase_value', 15, 2)->default(0);
            $table->string('condition')->default('good')->index();
            $table->string('status')->default('active')->index();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('maintenance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_staff_id')->nullable()->constrained('staff')->nullOnDelete();
            $table->date('scheduled_at');
            $table->date('completed_at')->nullable();
            $table->string('maintenance_type');
            $table->string('status')->default('scheduled')->index();
            $table->decimal('estimated_cost', 15, 2)->default(0);
            $table->decimal('actual_cost', 15, 2)->default(0);
            $table->text('actions')->nullable();
            $table->text('components')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });

        Schema::create('push_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('endpoint');
            $table->string('endpoint_hash', 64);
            $table->text('public_key');
            $table->text('auth_token');
            $table->string('content_encoding')->default('aes128gcm');
            $table->string('device_name')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'endpoint_hash']);
        });

        Schema::create('app_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type')->index();
            $table->string('title');
            $table->text('message');
            $table->string('url')->nullable();
            $table->timestamp('read_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action')->index();
            $table->string('subject_type');
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
            $table->index(['subject_type', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('app_notifications');
        Schema::dropIfExists('push_subscriptions');
        Schema::dropIfExists('maintenance_records');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('daily_reports');
        Schema::dropIfExists('staff');
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('location_id');
            $table->dropColumn(['role', 'phone', 'is_active']);
        });
        Schema::dropIfExists('locations');
    }
};
