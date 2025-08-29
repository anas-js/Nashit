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
        Schema::create('boards', function (Blueprint $table) {
            $table->id("board_number");
            $table->uuid("id")->unique();
            $table->string("name");
            $table->string("src_image")->nullable();
            $table->boolean("notifs")->default(true);
            $table->boolean("private")->default(true);
            $table->float("ratio",5,2)->default(0);
            $table->integer("lists_number")->default(1);
            $table->integer("tasks_number")->default(1);
            $table->integer("tasks_done")->default(0);
            $table->foreignId("user_id");
            $table->dateTime("last_activity");
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boards');
    }
};
