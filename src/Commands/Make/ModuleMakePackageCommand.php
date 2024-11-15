<?php

namespace SequelONE\sOne\Modules\Commands\Make;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Console\Command\Command as CommandAlias;

class ModuleMakePackageCommand extends Command
{
    protected $signature = 'make:module:package {vendor} {name} {nameClass}';
  	/*
  	* php artisan make:module:package SequelONE sone-blog Blog
  	*/
    protected $description = 'Create a new module in packages/{vendor}/{name}';

    public function handle(): int
    {
        $vendor = $this->argument('vendor');
        $moduleName = $this->argument('name');
        $nameClass = $this->argument('nameClass');
      	$vendorLowerName = Str::lower($vendor);

        $modulePath = base_path("packages/{$vendorLowerName}/{$moduleName}");

        if (File::exists($modulePath)) {
            $this->error("Module '{$moduleName}' already exists!");
            return CommandAlias::FAILURE;
        }

        $this->createModuleStructure($modulePath, $vendor, $moduleName, $nameClass);

        $this->info("Module '{$moduleName}' has been created successfully.");
        return CommandAlias::SUCCESS;
    }

    protected function createModuleStructure($modulePath, $vendor, $moduleName, $nameClass): void
    {
        $studlyName = Str::studly($moduleName);
        $lowerName = Str::snake($moduleName);
		$vendorLowerName = Str::lower($vendor);
        $namespace = "{$vendor}\\sOne\\Modules\\{$nameClass}";
        $appFolderName = 'src';

        // Создаем директории для модуля
        $directories = [
            "{$appFolderName}/Controllers",
            "{$appFolderName}/Models",
            "{$appFolderName}/Routes",
            "{$appFolderName}/Resources/views",
            "{$appFolderName}/Database/migrations",
        ];

        foreach ($directories as $dir) {
            File::makeDirectory("{$modulePath}/{$dir}", 0755, true);
        }

        // Генерация файлов из шаблонов
        $this->generateFromStub("helpers.stub", "{$modulePath}/src/helpers.php");
        $this->generateFromStub("controller.stub", "{$modulePath}/src/Controllers/{$nameClass}Controller.php", [
            '$NAMESPACE$' => $namespace,
            '$CONTROLLER$' => $nameClass . 'Controller',
            '$ROUTE_PREFIX$' => $vendorLowerName
        ]);

        // Генерация файла маршрутов
        $this->generateFromStub("web.stub", "{$modulePath}/src/Routes/web.php", [
            '$ROUTE_PREFIX$' => Str::snake($nameClass),
            '$CONTROLLER$' => "{$namespace}\\Controllers\\{$nameClass}Controller",
        ]);

        // Генерация модели
        $this->generateFromStub("model.stub", "{$modulePath}/src/Models/{$nameClass}.php", [
            '$NAMESPACE$' => $namespace,
            '$TABLE_NAME$' => Str::snake($nameClass) . 's',
            '$MODEL$' => $nameClass,
        ]);

        // Генерация ServiceProvider
        $this->generateFromStub("provider.stub", "{$modulePath}/src/{$nameClass}ServiceProvider.php", [
            '$NAMESPACE$' => $namespace,
            '$PROVIDER$' => $nameClass . 'ServiceProvider',
            '$MODULE_PATH$' => $appFolderName,
          	'$LOWER_NAME$' => $vendorLowerName,
            '$ROUTE_FILE$' => 'Routes/web.php',
        ]);

        // Генерация Blade-шаблона
        $this->generateFromStub("view.stub", "{$modulePath}/src/Resources/views/index.blade.php", [
            '$MODULE_NAME$' => $studlyName
        ]);

        // Генерация миграции
        $migrationFileName = date('Y_m_d_His') . "_create_{$lowerName}_table.php";
        $this->generateFromStub("migration.stub", "{$modulePath}/src/Database/migrations/{$migrationFileName}", [
            '$TABLE_NAME$' => Str::snake($nameClass) . 's',
            '$MODEL_NAME$' => $nameClass,
        ]);

        // Генерация файла composer.json
        $this->generateFromStub("composer.stub", "{$modulePath}/composer.json", [
            '$AUTHOR_NAME$' => 'SEQUEL.ONE',
            '$AUTHOR_EMAIL$' => 'admin@sequel.one',
            '$VENDOR$' => $vendorLowerName,
            '$LOWER_NAME$' => $lowerName,
            '$MODULE_NAMESPACE$' => $vendor,
            '$STUDLY_NAME$' => $nameClass,
            '$APP_FOLDER_NAME$' => $appFolderName,
        ]);
		
		// Генерация файла README.md
        $this->generateFromStub("readme.stub", "{$modulePath}/README.md", [
            '$AUTHOR_NAME$' => 'SEQUEL.ONE',
            '$AUTHOR_EMAIL$' => 'admin@sequel.one',
            '$VENDOR_LOWER$' => $vendorLowerName,
            '$MODULE_NAME$' => $moduleName,
            '$VENDOR$' => $vendor,
            '$CLASS_NAME$' => $nameClass,
            '$APP_FOLDER_NAME$' => $appFolderName,
        ]);
		
		// Генерация файла LICENSE.md
        $this->generateFromStub("license.stub", "{$modulePath}/LICENSE.md", [
            '$AUTHOR_NAME$' => 'SEQUEL.ONE',
            '$AUTHOR_EMAIL$' => 'admin@sequel.one',
            '$VENDOR_LOWER$' => $vendorLowerName,
            '$MODULE_NAME$' => $moduleName,
            '$VENDOR$' => $vendor,
            '$CLASS_NAME$' => $nameClass,
            '$APP_FOLDER_NAME$' => $appFolderName,
        ]);

        $this->refreshAutoload();
    }

    protected function generateFromStub($stubFile, $destinationPath, array $replacements = []): void
    {
        $stubPath = base_path("vendor/sequelone/sone-modules/src/Commands/stubs/package/{$stubFile}");

        if (!File::exists($stubPath)) {
            $this->error("Stub file '{$stubFile}' not found.");
            return;
        }

        $content = File::get($stubPath);

        // Заменяем плейсхолдеры на значения
        foreach ($replacements as $key => $value) {
            $content = str_replace($key, $value, $content);
        }

        // Логируем успешное создание файла
        if (File::put($destinationPath, $content)) {
            $this->info("Created: {$destinationPath}");
        } else {
            $this->error("Failed to create: {$destinationPath}");
        }
    }

    protected function refreshAutoload()
    {
        $this->info("Refreshing autoload and clearing cache...");
        exec('composer dump-autoload');
        exec('php artisan optimize:clear');
        exec('php artisan route:clear');
    }
}
