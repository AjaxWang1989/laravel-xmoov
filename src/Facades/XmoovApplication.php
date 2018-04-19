<?php
/**
 * Created by PhpStorm.
 * User: wang
 * Date: 2018/4/19
 * Time: 下午5:34
 */

namespace Zoran\LaravelXmoov\Facades;


use Illuminate\Support\Facades\Facade;
use Zoran\Xmoov\Application;

class XmoovApplication extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Application::class;
    }
}