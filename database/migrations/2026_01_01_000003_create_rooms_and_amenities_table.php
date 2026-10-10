<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_amenities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->decimal('base_price_per_night', 12, 2)->default(0.00);
            $table->unsignedInteger('max_adults')->default(2);
            $table->unsignedInteger('max_children')->default(1);
            $table->unsignedInteger('total_capacity')->default(2);
            $table->unsignedInteger('size_sqft')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['branch_id', 'slug']);
            $table->index(['branch_id', 'is_active']);
        });

        Schema::create('room_type_amenities', function (Blueprint $table) {
            $table->foreignId('room_type_id')->constrained('room_types')->cascadeOnDelete();
            $table->foreignId('room_amenity_id')->constrained('room_amenities')->cascadeOnDelete();
            $table->primary(['room_type_id', 'room_amenity_id']);
        });

        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('room_type_id')->constrained('room_types')->cascadeOnDelete();
            $table->string('room_number', 20);
            $table->string('floor', 20)->default('1');
            $table->enum('operational_status', ['available', 'cleaning', 'maintenance', 'out_of_order'])->default('available');
            $table->string('maintenance_reason')->nullable();
            $table->text('operational_notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['branch_id', 'room_number']);
            $table->index(['branch_id', 'operational_status']);
            $table->index(['branch_id', 'room_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('room_type_amenities');
        Schema::dropIfExists('room_types');
        Schema::dropIfExists('room_amenities');
    }
};
