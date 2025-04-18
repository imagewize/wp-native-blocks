<?php

namespace Imagewize\SageNativeBlockPackage\Providers;

use Illuminate\Support\ServiceProvider;
use Imagewize\SageNativeBlockPackage\Console\SageNativeBlockCommand;
use Imagewize\SageNativeBlockPackage\SageNativeBlock;

class SageNativeBlockServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('SageNativeBlock', function () {
            return new SageNativeBlock($this->app);
        });

        $this->mergeConfigFrom(
            __DIR__.'/../../config/sage-native-block.php',
            'sage-native-block'
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/sage-native-block.php' => $this->app->configPath('sage-native-block.php'),
        ], 'config');

        $this->loadViewsFrom(
            __DIR__.'/../../resources/views',
            'SageNativeBlock',
        );

        $this->commands([
            SageNativeBlockCommand::class,
        ]);

        $this->app->make('SageNativeBlock');
    }
}
