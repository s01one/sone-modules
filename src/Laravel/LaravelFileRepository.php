<?php

namespace SequelONE\sOne\Modules\Laravel;

use SequelONE\sOne\Modules\FileRepository;
use SequelONE\sOne\Modules\Module;

class LaravelFileRepository extends FileRepository
{
    /**
     * {@inheritdoc}
     */
    protected function createModule(...$args)
    {
        return new Module(...$args);
    }
}
