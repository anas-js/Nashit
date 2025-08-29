<?php

namespace App\Console\Commands;

use App\Models\Board\Board;
use Illuminate\Console\Command;

class resetBoardLists extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-board-lists';

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
       $board = Board::find('9c698c9f-8f70-4c5a-8897-df516d90eeb3');
       $board->lists()->get()->each(function ($list) {
        $list->tasks()->delete();
        for($i=1;$i<=50;$i++) {
            $list->tasks()->create([
                "name" => "مهمة $i في $list->name"
            ]);
        };
        $list->update([
            "tasks_number" => 50
        ]);
       });
       $this->info("RESET DONE!!");
    }
}
