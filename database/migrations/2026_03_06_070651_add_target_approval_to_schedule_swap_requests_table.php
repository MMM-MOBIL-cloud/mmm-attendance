<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('schedule_swap_requests', function (Blueprint $table) {

        $table->string('target_status')
        ->default('Pending')
        ->after('status');

    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
{
    Schema::table('schedule_swap_requests', function (Blueprint $table) {

        $table->dropColumn('target_status');

    });
}
};
