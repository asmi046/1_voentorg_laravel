<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class TestRandomString extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:random-string';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Тестовая команда: выводит случайную строку каждые 3 секунды';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info(now()->format('H:i:s').' | '.Str::random(16));

        return self::SUCCESS;
    }
}
