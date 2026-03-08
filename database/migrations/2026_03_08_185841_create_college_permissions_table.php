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
        Schema::create('college_permissions', function (Blueprint $table) {
    $table->id();

    $table->foreignId('user_id');

    $table->date('date');
    $table->time('start_time');
    $table->time('end_time');
    $table->text('reason')->nullable();

    $table->date('replace_date')->nullable();
    $table->time('replace_start')->nullable();
    $table->time('replace_end')->nullable();
    $table->text('replace_reason')->nullable();

    $table->string('status')->default('pending');

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('college_permissions');
    }
};
