<?php
/**
 * Created by PhpStorm.
 * User: wang
 * Date: 2018/4/19
 * Time: 下午5:45
 */

namespace Zoran\LaravelXmoov;


use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use Zoran\LaravelXmoov\Console\EncryptAvi;
use Zoran\LaravelXmoov\Console\EncryptFlv;
use Zoran\LaravelXmoov\Console\EncryptMp4;
use Zoran\LaravelXmoov\Console\XmoovTokenSecretGenerateCommand;
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
            EncryptFlv::class,
            XmoovTokenSecretGenerateCommand::class
        ]);
        $config = config('xmoov');

        $storagePath = storage_path($config['storage_path']);

        $this->app->singleton('xmoov.video', function ($app) use($config, $storagePath){
            $request = $app['request'];
            return new VideoServer($request, $storagePath, $config);
        });

        $this->app->singleton('xmoov.audio', function ($app) use($config, $storagePath){
            $request = $app['request'];
            return new AudioServer($request, $storagePath, $config);
        });

        $this->app->singleton('xmoov.player', function ($app) use($config, $storagePath){
            $request = $app['request'];
            return new PlayerServer($request, $storagePath, $config);
        });

        $this->app->singleton('xmoov.download', function ($app) use($config, $storagePath){
            $request = $app['request'];
            return new DownloadServer($request, $storagePath, $config);
        });

        $this->app->singleton('xmoov.embed', function ($app) use($config, $storagePath){
            $request = $app['request'];
            return new EmbedServer($request, $storagePath, $config);
        });

        $this->app->singleton('xmoov.file.info', function ($app) use($config, $storagePath){
            $request = $app['request'];
            return new FileInfo($request, $storagePath, $config);
        });

        $this->app->singleton('xmoov.file', function ($app) use($config, $storagePath){
            $request = $app['request'];
            return new FileServer($request, $storagePath, $config);
        });

        $this->app->singleton('xmoov.image', function ($app) use($config, $storagePath){
            $request = $app['request'];
            return new ImageServer($request, $storagePath, $config);
        });

        $this->app->singleton('xmoov.private.image', function ($app) use($config, $storagePath){
            $request = $app['request'];
            $server = new PrivateImageServer($request, $storagePath, $config);
            $server->setToken(app('xmoov.token'));
            return $server;
        });

        $this->app->singleton('xmoov.private.download', function ($app) use($config, $storagePath){
            $request = $app['request'];
            $server = new PrivateDownloadServer($request, $storagePath, $config);
            $server->setToken(app('xmoov.token'));
            return $server;
        });

        $this->app->singleton('xmoov.token', function ($app){
            $request = $app['request'];
            return new XmoovStreamToken(config('xmoov.token.secret'), config('xmoov.token.expires'), $request);
        });

        $this->app->singleton(FlvStreamHandle::class, function (){
            return new FlvStreamHandle();
        });

        $this->app->singleton('xmoov', function ($app){
            $streamHandler = config('xmoov.stream_handler');
            $streamHandler = $streamHandler ? $app[$streamHandler] : null;
            $server = config('xmoov.server');
            $server = $server ? $app[$server] : null;
            return new XmoovApplication(config('xmoov'), $server, $streamHandler);
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
