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
    Schema::table('users', function (Blueprint $table) {

        $table->boolean('can_swap_schedule')->default(true);
        $table->boolean('can_approve_swap')->default(true);
        $table->boolean('can_student_leave')->default(false);
        $table->boolean('can_general_leave')->default(false); // izin tidak dihitung jam kerja

    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
