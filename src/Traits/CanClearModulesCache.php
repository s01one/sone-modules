<?php

namespace SequelONE\sOne\Modules\Traits;

trait CanClearModulesCache
{
    /**
     * Clear the modules cache if it is enabled
     */
    public function clearCache()
    {
        $this->laravel['modules']->resetModules();
    }
}
