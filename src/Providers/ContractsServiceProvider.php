<?php

namespace SequelONE\sOne\Modules\Providers;

use Illuminate\Support\ServiceProvider;
use SequelONE\sOne\Modules\Contracts\RepositoryInterface;
use SequelONE\sOne\Modules\Laravel\LaravelFileRepository;

class ContractsServiceProvider extends ServiceProvider
{
    /**
     * Register some binding.
     */
    public function register()
    {
        $this->app->bind(RepositoryInterface::class, LaravelFileRepository::class);
    }
}
