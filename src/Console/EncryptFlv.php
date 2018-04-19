<?php

namespace Zoran\LaravelXmoov\Console;

use App\Utils\FlvStreamHandle;
use Illuminate\Console\Command;

class EncryptFlv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flv:encrypt {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->argument('file');

        $filename = basename($file, '.flv');
        $tempFile     = dirname($file) . DIRECTORY_SEPARATOR . $filename . '.temp.flv';
        rename($file, $tempFile);
        app(FlvStreamHandle::class)->encrypt($tempFile);
    }
}
