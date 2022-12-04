<?php

namespace Customberg\PHP;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Customberg\PHP\Commands\MakeBlock;

class CustombergServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('customberg')
            ->hasConfigFile()
            ->hasAssets()
            ->hasViews()
            ->hasRoute('customberg')
            ->hasCommands([MakeBlock::class]);
    }

    public function packageRegistered()
    {
        $this->app->singleton(Customberg::class, function () {
            $config = config('customberg');
            return Customberg::getInstance($config);
        });
    }
}
