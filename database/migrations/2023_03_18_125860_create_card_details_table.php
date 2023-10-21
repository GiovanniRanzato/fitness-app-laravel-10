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
        Schema::create('card_details', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('quantity')->nullable();
            $table->integer('time_duration')->nullable();
            $table->integer('time_recovery')->nullable();
            $table->float('weight')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('card_id')->constrained('cards')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('exercise_id')->nullable()->constrained('exercises')->cascadeOnUpdate()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('card_details');
    }
};
