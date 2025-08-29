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
        Schema::create('registered', function (Blueprint $table) {
            $table->id();
            $table->integer("courses_limit")->default(5);
            $table->integer('lessons_limit')->default(150);
            $table->integer("boards_limit")->default(2);
            $table->integer('lists_limit')->default(3);
            $table->integer('tasks_limit')->default(50);
            $table->integer('days_done_limit')->default(200);
            $table->integer('note_limit')->default(10000);
            $table->foreignId("user_id");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registered');
    }
};
