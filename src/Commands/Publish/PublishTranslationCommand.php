<?php

namespace SequelONE\sOne\Modules\Commands\Publish;

use SequelONE\sOne\Modules\Commands\BaseCommand;
use SequelONE\sOne\Modules\Publishing\LangPublisher;

class PublishTranslationCommand extends BaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:publish-translation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish a module\'s translations to the application';

    public function executeAction($name): void
    {
        $module = $this->getModuleModel($name);

        $this->components->task("Publishing Translations <fg=cyan;options=bold>{$module->getName()}</> Module", function () use ($module) {
            with(new LangPublisher($module))
                ->setRepository($this->laravel['modules'])
                ->setConsole($this)
                ->publish();
        });
    }

    public function getInfo(): ?string
    {
        return 'Publishing module translations ...';
    }
}
