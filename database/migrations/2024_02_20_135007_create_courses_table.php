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
        Schema::create('courses', function (Blueprint $table) {
            $table->id("course_number");
            $table->uuid("id")->unique();
            $table->string("name");
            $table->string("src_image")->nullable();
            $table->boolean("notifs")->default(true);
            $table->boolean("inSpace");
            $table->boolean("private")->default(true);
            $table->boolean("finish")->default(false);
            $table->dateTime("date_finish"); // user time
            $table->dateTime("date_done")->nullable();  // user time
            $table->float("ratio",5,2)->default(0);
            $table->integer("done_days");
            $table->integer("workDays");
            $table->integer("lessons_number");
            $table->set("weekend",[0,1,2,3,4,5,6])->default(null)->nullable();
            $table->dateTime("last_activity");
            // $table->integer("lesson_for_day");
            // $table->boolean("have_single_lessons");
            // $table->integer("double_lessons");
            $table->foreignId("user_id");
            $table->timestamps();
        });
        // $table->increments("");
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
