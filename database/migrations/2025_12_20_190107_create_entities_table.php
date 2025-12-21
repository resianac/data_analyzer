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
        Schema::create('entities', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->nullable();
            $table->string('title');
            $table->string('source', 50);
            $table->string('type', 50)->comment("car, flat...");
            $table->schemalessAttributes('data');
            $table->timestamp('external_last_update')->nullable();
            $table->timestamps();

            $table->unique(['external_id', 'source']);
            $table->index(['source', 'type']);
            $table->index(['title']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entities');
    }
};
