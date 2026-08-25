<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->string('barcode', 40)->nullable()->unique()->after('asset_tag');
        });

        DB::table('assets')->orderBy('id')->get(['id', 'barcode'])->each(function (object $asset): void {
            if ($asset->barcode === null) {
                DB::table('assets')->where('id', $asset->id)->update([
                    'barcode' => 'ASTBC-'.str_pad((string) $asset->id, 8, '0', STR_PAD_LEFT),
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropUnique(['barcode']);
            $table->dropColumn('barcode');
        });
    }
};
