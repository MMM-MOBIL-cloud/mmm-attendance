<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedule_swap_requests', function (Blueprint $table) {

            $table->id();

            $table->foreignId('requester_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->foreignId('target_user_id')
                  ->nullable()
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->date('swap_date');

            $table->enum('type', ['self','swap']);

            $table->enum('status', ['Pending','Approved','Rejected'])
                  ->default('Pending');

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_swap_requests');
    }
};
