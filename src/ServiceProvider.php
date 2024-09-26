<?php

namespace Mertcanureten\LaravelPostmanCollectionServiceProvider;

use Illuminate\Support\ServiceProvider;

class LaravelPostmanCollectionServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->commands([
            Console\ExportPostmanCollection::class,
        ]);
    }

    public function register()
    {
        //
    }
}