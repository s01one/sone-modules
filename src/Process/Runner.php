<?php

namespace SequelONE\sOne\Modules\Process;

use SequelONE\sOne\Modules\Contracts\RepositoryInterface;
use SequelONE\sOne\Modules\Contracts\RunableInterface;

class Runner implements RunableInterface
{
    /**
     * The module instance.
     *
     * @var RepositoryInterface
     */
    protected $module;

    public function __construct(RepositoryInterface $module)
    {
        $this->module = $module;
    }

    /**
     * Run the given command.
     *
     * @param  string  $command
     */
    public function run($command)
    {
        passthru($command);
    }
}
