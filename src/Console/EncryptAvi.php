<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>　
// +----------------------------------------------------------------------

namespace Zoran\LaravelXmoov\Console;

use Zoran\Xmoov\FlvStreamHandle;
use Illuminate\Console\Command;

class EncryptAvi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'avi:encrypt {file}';

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
        $file     = $this->argument('file');
        $filename = basename($file, '.avi');
        $dir      = dirname($file);
        $tempFile = "{$dir}" . DIRECTORY_SEPARATOR . "{$filename}.temp.flv";
        app('xmoov')->encrypt($file, $tempFile);
    }
}