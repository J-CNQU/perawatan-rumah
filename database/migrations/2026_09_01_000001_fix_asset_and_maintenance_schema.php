<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('assets')) {
            $database = DB::connection()->getDriverName();

            if ($database === 'mysql') {
                DB::statement("ALTER TABLE `assets` MODIFY COLUMN `condition` ENUM('Normal', 'Perlu Servis', 'Rusak') NOT NULL DEFAULT 'Normal'");
            } else {
                Schema::table('assets', function (Blueprint $table) {
                    $table->string('condition', 20)->default('Normal')->change();
                });
            }
        }

        if (Schema::hasTable('maintenance_logs') && ! Schema::hasColumn('maintenance_logs', 'type')) {
            Schema::table('maintenance_logs', function (Blueprint $table) {
                $table->string('type', 30)->default('Routine Checkup')->after('asset_id');
            });
        }

        if (Schema::hasTable('maintenance_logs') && Schema::hasColumn('maintenance_logs', 'type')) {
            Schema::table('maintenance_logs', function (Blueprint $table) {
                $table->string('type', 30)->default('Routine Checkup')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('assets')) {
            DB::statement("ALTER TABLE `assets` MODIFY COLUMN `condition` ENUM('New', 'Used') NOT NULL DEFAULT 'New'");
        }

        if (Schema::hasTable('maintenance_logs') && Schema::hasColumn('maintenance_logs', 'type')) {
            Schema::table('maintenance_logs', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }
    }
};
