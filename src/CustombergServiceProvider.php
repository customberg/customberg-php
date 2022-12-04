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
            ->hasViews()
            ->hasCommand(MakeBlock::class);
    }
}
