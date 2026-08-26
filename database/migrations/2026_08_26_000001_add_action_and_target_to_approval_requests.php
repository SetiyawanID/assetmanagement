<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('approval_requests', function (Blueprint $table) {
            $table->string('action', 20)->default('create')->after('type');
            $table->unsignedBigInteger('target_id')->nullable()->after('payload');
            $table->index(['type', 'action', 'target_id']);
        });
    }

    public function down(): void
    {
        Schema::table('approval_requests', function (Blueprint $table) {
            $table->dropIndex(['type', 'action', 'target_id']);
            $table->dropColumn(['action', 'target_id']);
        });
    }
};
