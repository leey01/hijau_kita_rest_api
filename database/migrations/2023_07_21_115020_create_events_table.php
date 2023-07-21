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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_category_id')
                ->constrained('sub_categories')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('name');
            $table->text('description');
            $table->string('image');
            $table->string('regency');
            $table->string('registration_link');
            $table->integer('reward');
            $table->integer('code')->nullable();
            $table->date('date_start');
            $table->date('date_end');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
