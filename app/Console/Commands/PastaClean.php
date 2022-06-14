<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Pasta;

class PastaClean extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pasta:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleans up expired pastas.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $counter = 0;
        $pastas = Pasta::select('uuid')->where('expires_at', '<=', now())->get();

        $pastas->each(function (Pasta $pasta) use (&$counter) {
            $pasta->delete();
            $counter++;
        });

        Log::info("Cleaned up $counter expired pastas.");
    }
}
