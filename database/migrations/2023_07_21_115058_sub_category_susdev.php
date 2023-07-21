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
        Schema::create('sub_category_susdev', function (Blueprint $table) {
            $table->foreignId('sub_category_id')
                ->constrained('sub_categories')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('susdev_id')
                ->constrained('susdev_goals')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
