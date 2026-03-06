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

        $table->date('from_date')->nullable();
        $table->date('to_date')->nullable();

        $table->dropColumn('swap_date');

    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    Schema::table('schedule_swap_requests', function (Blueprint $table) {

        $table->date('swap_date')->nullable();

        $table->dropColumn(['from_date','to_date']);

    });
    }
};
