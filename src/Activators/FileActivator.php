<?php

namespace SequelONE\sOne\Modules\Activators;

use Illuminate\Config\Repository as Config;
use Illuminate\Container\Container;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use SequelONE\sOne\Modules\Contracts\ActivatorInterface;
use SequelONE\sOne\Modules\Module;

class FileActivator implements ActivatorInterface
{
    /**
     * laravel Filesystem instance
     *
     * @var Filesystem
     */
    private $files;

    /**
     * laravel config instance
     *
     * @var Config
     */
    private $config;

    /**
     * Array of modules activation statuses
     *
     * @var array
     */
    private $modulesStatuses;

    /**
     * File used to store activation statuses
     *
     * @var string
     */
    private $statusesFile;

    public function __construct(Container $app)
    {
        $this->files = $app['files'];
        $this->config = $app['config'];
        $this->statusesFile = $this->config('statuses-file');
        $this->modulesStatuses = $this->readJson();
    }

    /**
     * Get the path of the file where statuses are stored
     */
    public function getStatusesFilePath(): string
    {
        return $this->statusesFile;
    }

    /**
     * {@inheritDoc}
     */
    public function reset(): void
    {
        if ($this->files->exists($this->statusesFile)) {
            $this->files->delete($this->statusesFile);
        }
        $this->modulesStatuses = [];
    }

    /**
     * {@inheritDoc}
     */
    public function enable(Module $module): void
    {
        $this->setActiveByName($module->getName(), true);
    }

    /**
     * {@inheritDoc}
     */
    public function disable(Module $module): void
    {
        $this->setActiveByName($module->getName(), false);
    }

    /**
     * {@inheritDoc}
     */
    public function hasStatus(Module|string $module, bool $status): bool
    {
        $name = $module instanceof Module ? $module->getName() : $module;

        if (! isset($this->modulesStatuses[$name])) {
            return $status === false;
        }

        return $this->modulesStatuses[$name] === $status;
    }

    /**
     * {@inheritDoc}
     */
    public function setActive(Module $module, bool $active): void
    {
        $this->setActiveByName($module->getName(), $active);
    }

    /**
     * {@inheritDoc}
     */
    public function setActiveByName(string $name, bool $status): void
    {
        $this->modulesStatuses[$name] = $status;
        $this->writeJson();
    }

    /**
     * {@inheritDoc}
     */
    public function delete(Module $module): void
    {
        if (! isset($this->modulesStatuses[$module->getName()])) {
            return;
        }
        unset($this->modulesStatuses[$module->getName()]);
        $this->writeJson();
    }

    /**
     * Writes the activation statuses in a file, as json
     */
    private function writeJson(): void
    {
        $this->files->put($this->statusesFile, json_encode($this->modulesStatuses, JSON_PRETTY_PRINT));
    }

    /**
     * Reads the json file that contains the activation statuses.
     *
     * @throws FileNotFoundException
     */
    private function readJson(): array
    {
        if (! $this->files->exists($this->statusesFile)) {
            return [];
        }

        return $this->files->json($this->statusesFile);
    }

    /**
     * Reads a config parameter under the 'activators.file' key
     *
     * @return mixed
     */
    private function config(string $key, $default = null)
    {
        return $this->config->get('modules.activators.file.'.$key, $default);
    }
}
