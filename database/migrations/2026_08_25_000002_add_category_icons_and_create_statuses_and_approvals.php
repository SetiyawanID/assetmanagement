<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('icon', 50)->default('bi-folder')->after('name');
        });

        Schema::create('statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('color', 7)->default('#6c757d');
            $table->timestamps();
        });

        Schema::create('approval_requests', function (Blueprint $table) {
            $table->id();
            $table->string('type', 30);
            $table->json('payload');
            $table->foreignId('requested_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 20)->default('pending');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'type']);
        });

        DB::table('categories')->whereNull('icon')->update(['icon' => 'bi-folder']);

        foreach ([
            ['name' => 'Tersedia', 'slug' => 'available', 'color' => '#198754'],
            ['name' => 'Assigned', 'slug' => 'assigned', 'color' => '#0d6efd'],
            ['name' => 'Maintenance', 'slug' => 'maintenance', 'color' => '#ffc107'],
            ['name' => 'Retired', 'slug' => 'retired', 'color' => '#6c757d'],
        ] as $status) {
            DB::table('statuses')->updateOrInsert(['slug' => $status['slug']], array_merge($status, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        if (! DB::table('categories')->where('name', 'Laptop dan Network')->exists()) {
            DB::table('categories')->insert([
                'name' => 'Laptop dan Network',
                'icon' => 'bi-laptop',
                'description' => 'Perangkat laptop, komputer, dan jaringan.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_requests');
        Schema::dropIfExists('statuses');
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('icon');
        });
    }
};
