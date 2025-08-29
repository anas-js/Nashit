<?php

namespace App\Console\Commands;

use App\Models\Board\Board;
use Illuminate\Console\Command;

class createBoard extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:board';

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
        Board::create([
            'name' => "any",
            'user_id' => 4
        ]);
    }
}
