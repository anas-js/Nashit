<?php

namespace App\Console\Commands;

use App\Models\Course\Course;
use Illuminate\Console\Command;

class createCourse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:course';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
       Course::create([
            "name" => "name",
            "done_days" => "4",
            "lessons_number" => "3",
            "weekend"=> [5,4],
        ]);
    }
}
