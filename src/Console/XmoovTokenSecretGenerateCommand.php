<?php
/**
 * Created by PhpStorm.
 * User: wang
 * Date: 2018/4/20
 * Time: 下午4:55
 */

namespace Zoran\LaravelXmoov\Console;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class XmoovTokenSecretGenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xstoken:generate {key}';

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
        $key     = $this->argument('key');
        $oldSecret = config('xmoov.token.secret');
        $newSecret = Hash::make(str_random(), [
            'slat' => $key,
            'time' => time()
        ]);
        $file = config_path('xmoov.php');
        $content = file_get_contents($file);
        $content = preg_replace($oldSecret, $newSecret, $content);
        file_put_contents($file, $content);
    }
}