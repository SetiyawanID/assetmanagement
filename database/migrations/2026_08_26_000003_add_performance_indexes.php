<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index(['role', 'division_id'], 'users_role_division_index');
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->index('name', 'assets_name_index');
        });

        Schema::table('approval_requests', function (Blueprint $table) {
            $table->index(['requested_by', 'status'], 'approvals_requester_status_index');
            $table->index(['type', 'target_id', 'status'], 'approvals_target_status_index');
        });
    }

    public function down(): void
    {
        Schema::table('approval_requests', function (Blueprint $table) {
            $table->dropIndex('approvals_requester_status_index');
            $table->dropIndex('approvals_target_status_index');
        });
        Schema::table('assets', fn (Blueprint $table) => $table->dropIndex('assets_name_index'));
        Schema::table('users', fn (Blueprint $table) => $table->dropIndex('users_role_division_index'));
    }
};
