<?php
/**
 * Created by PhpStorm.
 * User: wang
 * Date: 2018/4/19
 * Time: 下午5:45
 */

namespace Zoran\LaravelXmoov;


use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use Zoran\LaravelXmoov\Console\EncryptAvi;
use Zoran\LaravelXmoov\Console\EncryptFlv;
use Zoran\LaravelXmoov\Console\EncryptMp4;
use Zoran\Xmoov\Application as XmoovApplication;
use Zoran\Xmoov\FlvStreamHandle;
use Zoran\Xmoov\Servers\AudioServer;
use Illuminate\Foundation\Application as LaravelApplication;
use Laravel\Lumen\Application as LumenApplication;
use Zoran\Xmoov\Servers\DownloadServer;
use Zoran\Xmoov\Servers\EmbedServer;
use Zoran\Xmoov\Servers\FileInfo;
use Zoran\Xmoov\Servers\FileServer;
use Zoran\Xmoov\Servers\ImageServer;
use Zoran\Xmoov\Servers\PlayerServer;
use Zoran\Xmoov\Servers\PrivateDownloadServer;
use Zoran\Xmoov\Servers\PrivateImageServer;
use Zoran\Xmoov\Servers\VideoServer;
use Zoran\Xmoov\Stream\XmoovStreamToken;

class LaravelXmoovServiceProvider extends ServiceProvider
{
    public function register() {
        $this->commands([
            EncryptAvi::class,
            EncryptMp4::class,
            EncryptFlv::class
        ]);
        $config = config('xmoov');

        $storagePath = storage_path($config['storage_path']);

        $this->app->singleton('xmoov.audio', function (Request $request) use($config, $storagePath){
            return new AudioServer($request, $storagePath, $config);
        });

        $this->app->singleton('xmoov.player', function (Request $request) use($config, $storagePath){
            return new PlayerServer($request, $storagePath, $config);
        });

        $this->app->singleton('xmoov.download', function (Request $request) use($config, $storagePath){
            return new DownloadServer($request, $storagePath, $config);
        });

        $this->app->singleton('xmoov.embed', function (Request $request) use($config, $storagePath){
            return new EmbedServer($request, $storagePath, $config);
        });

        $this->app->singleton('xmoov.file.info', function (Request $request) use($config, $storagePath){
            return new FileInfo($request, $storagePath, $config);
        });

        $this->app->singleton('xmoov.file', function (Request $request) use($config, $storagePath){
            return new VideoServer($request, $storagePath, $config);
        });

        $this->app->singleton('xmoov.file', function (Request $request) use($config, $storagePath){
            return new FileServer($request, $storagePath, $config);
        });

        $this->app->singleton('xmoov.image', function (Request $request) use($config, $storagePath){
            return new ImageServer($request, $storagePath, $config);
        });

        $this->app->singleton('xmoov.private.image', function (Request $request) use($config, $storagePath){
            $server = new PrivateImageServer($request, $storagePath, $config);
            $server->setToken(app('xmoov.token'));
            return $server;
        });

        $this->app->singleton('xmoov.private.download', function (Request $request) use($config, $storagePath){
            $server = new PrivateDownloadServer($request, $storagePath, $config);
            $server->setToken(app('xmoov.token'));
            return $server;
        });

        $this->app->singleton('xmoov.token', function (Request $request){
            return new XmoovStreamToken(config('xmoov.token.secret'), config('xmoov.token.expires'), $request);
        });

        $this->app->singleton('xmoov', function (){
            return new XmoovApplication(config('xmoov'), null, app(FlvStreamHandle::class));
        });
    }

    /**
     * Setup the config.
     *
     * @return void
     */
    protected function setupConfig()
    {
        $source = realpath(__DIR__.'/Config/xmoov.php');

        if ($this->app instanceof LaravelApplication) {
            if ($this->app->runningInConsole()) {
                $this->publishes([
                    $source => config_path('xmoov.php'),
                ]);
            }
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('xmoov');
        }

        $this->mergeConfigFrom($source, 'wechat');
    }

    public function boot() {
        $this->setupConfig();
    }
}