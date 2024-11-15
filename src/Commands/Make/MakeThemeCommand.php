<?php

namespace SequelONE\sOne\Modules\Commands\Make;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeThemeCommand extends Command
{
    protected $signature = 'make:theme {vendor} {name} {nameClass}';
    protected $description = 'Create a new theme in packages/{vendor}/{name}';

  	/*
  	* php artisan make:module:package SequelONE sone-theme-default Default
  	*/

    public function handle(): int
    {
		$vendor = $this->argument('vendor');
      	$vendorLowerName = Str::lower($this->argument('vendor'));
        $nameModule = $this->argument('name');
      	$themeName = $this->argument('nameClass');
      	$lowerName = Str::snake($nameModule);
        $authorName = 'SEQUEL.ONE';
        $authorEmail = 'admin@sequel.one';
      	$appFolderName = 'src';

        $studlyThemeName = Str::studly($themeName);
        $lowerThemeName = Str::snake($themeName);

        $themePath = base_path("packages/{$vendorLowerName}/{$nameModule}");

        if (File::exists($themePath)) {
            $this->error("Theme '{$themeName}' already exists!");
            return Command::FAILURE;
        }

        $this->createThemeStructure($themePath, $appFolderName, $vendor, $studlyThemeName, $vendorLowerName, $lowerName, $authorName, $authorEmail);
        $this->info("Theme '{$themeName}' has been created successfully.");
        return Command::SUCCESS;
    }

    protected function createThemeStructure($themePath, $appFolderName, $vendor, $themeName, $vendorLowerName, $lowerName, $authorName, $authorEmail): void
    {
        $directories = [
            'Controllers',
            'Views',
            'Views/layouts',
            'Assets/css',
            'Assets/js',
            'Assets/images',
        ];

        foreach ($directories as $dir) {
            File::makeDirectory("{$themePath}/{$dir}", 0755, true);
        }

        // Генерация файлов из шаблонов
        $this->generateFromStub("controller.stub", "{$themePath}/Controllers/{$themeName}Controller.php", [
            '$THEME_NAME$' => $themeName,
        ]);

        $this->generateFromStub("views/index.stub", "{$themePath}/Views/index.blade.php", [
            '$THEME_NAME$' => $themeName,
        ]);

        $this->generateFromStub("views/layout.stub", "{$themePath}/Views/layouts/app.blade.php", [
            '$THEME_NAME$' => $themeName,
        ]);

        $this->generateFromStub("theme.stub", "{$themePath}/theme.json", [
            '$THEME_NAME$' => $themeName,
            '$AUTHOR_NAME$' => $authorName,
            '$AUTHOR_EMAIL$' => $authorEmail,
        ]);
      
      	$this->generateFromStub("composer.stub", "{$themePath}/composer.json", [
            '$AUTHOR_NAME$' => 'SEQUEL.ONE',
            '$AUTHOR_EMAIL$' => 'admin@sequel.one',
            '$VENDOR$' => $vendorLowerName,
            '$LOWER_NAME$' => $lowerName,
            '$MODULE_NAMESPACE$' => $vendor,
            '$STUDLY_NAME$' => $themeName,
            '$APP_FOLDER_NAME$' => $appFolderName,
        ]);

        // Создаем файл стилей
        File::put("{$themePath}/Assets/css/style.css", "/* Styles for {$themeName} theme */");

        // Создаем файл JavaScript
        File::put("{$themePath}/Assets/js/script.js", "// JavaScript for {$themeName} theme");
    }

    protected function generateFromStub($stubFile, $destinationPath, array $replacements = []): void
    {
        $stubPath = base_path("vendor/s01one/sone-modules/src/Commands/stubs/themes/{$stubFile}");

        if (!File::exists($stubPath)) {
            $this->error("Stub file '{$stubFile}' not found.");
            return;
        }

        $content = File::get($stubPath);

        foreach ($replacements as $key => $value) {
            $content = str_replace($key, $value, $content);
        }

        File::put($destinationPath, $content);
    }
}
