<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id(); $table->string('name')->unique(); $table->text('description')->nullable(); $table->timestamps();
        });
        Schema::create('locations', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->string('floor')->nullable(); $table->text('description')->nullable(); $table->timestamps();
        });
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_tag')->unique(); $table->string('name');
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('brand')->nullable(); $table->string('model')->nullable(); $table->string('serial_number')->nullable()->unique();
            $table->date('purchase_date')->nullable(); $table->decimal('purchase_price', 12, 2)->nullable();
            $table->string('status')->default('available'); $table->string('condition')->default('good');
            $table->date('warranty_until')->nullable(); $table->text('notes')->nullable(); $table->timestamps();
            $table->index(['status', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets'); Schema::dropIfExists('locations'); Schema::dropIfExists('categories');
    }
};
