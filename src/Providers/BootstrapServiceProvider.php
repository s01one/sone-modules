<?php

namespace SequelONE\sOne\Modules\Providers;

use Illuminate\Support\ServiceProvider;
use SequelONE\sOne\Modules\Contracts\RepositoryInterface;
use SequelONE\sOne\Modules\Laravel\LaravelFileRepository;

class BootstrapServiceProvider extends ServiceProvider
{
    /**
     * Booting the package.
     */
    public function boot(): void
    {
        $this->getRepositoryInterface()->boot();
    }

    /**
     * Register the provider.
     */
    public function register(): void
    {
        $this->getRepositoryInterface()->register();
    }

    /**
     * @return LaravelFileRepository
     */
    public function getRepositoryInterface()
    {
        return $this->app[RepositoryInterface::class];
    }
}
