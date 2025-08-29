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
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->integer("index")->default(0);
            $table->string("name");
            $table->boolean("done")->default(false);
            $table->text("note")->default(null)->nullable();
            $table->dateTime("date_done")->default(null)->nullable(); // user time
            $table->dateTime("exp_date_done"); // user time
            $table->dateTime("last_exp_date")->default(null)->nullable(); // user time
            $table->foreignUuid("course_id");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
