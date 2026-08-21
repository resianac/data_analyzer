<?php

namespace App\Console\Commands\Sources;

use App\Services\Sources\Clients\Marketplace999\Actions\SearchFlatsAction;
use App\Services\Sources\Clients\RabotaMd\Actions\SearchJobsAction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:source {name : Source name, e.g. Marketplace999}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command makes new source of data with all structure';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = Str::studly($this->argument('name'));
        $basePath = app_path("Services/Sources/Clients/{$name}");

        if (File::isDirectory($basePath)) {
            $this->error("Source [{$name}] has already exist by path: {$basePath}");
            return self::FAILURE;
        }

        $this->makeDirectories($basePath);
        $this->makeFiles($name, $basePath);

        $this->info("Source [{$name}] successfully created: {$basePath}");

        return self::SUCCESS;
    }

    private function makeDirectories(string $basePath): void
    {
        $directories = [
            $basePath,
            "{$basePath}/Actions",
            "{$basePath}/Adapters",
            "{$basePath}/Data",
            "{$basePath}/Enums",
            "{$basePath}/Filters/Formatters",
            "{$basePath}/Filters/Variables",
            "{$basePath}/Filters/Variables/Html",
            "{$basePath}/Jobs",
            "{$basePath}/Normalizers",
        ];

        foreach ($directories as $directory) {
            File::ensureDirectoryExists($directory);
        }
    }

    private function makeFiles(string $name, string $basePath): void
    {
        $files = [
            "Actions/Orchestrate{$name}SearchAction.php" => 'orchestrate-action',
            "Adapters/{$name}DataAdapter.php"            => 'adapter',
            "Data/{$name}Data.php"                       => 'data',
            "Enums/{$name}SearchParam.php"               => 'param-enum',
            "Filters/Variables/Html/{$name}EntityVariables.php" => 'html-variables',
            "Normalizers/{$name}NormalizerFactory.php"   => 'normalizer-factory',
            "{$name}Client.php"                          => 'client',
            "{$name}Config.php"                          => 'config',
        ];

        foreach ($files as $relativePath => $stubName) {
            $this->makeFileFromStub($name, "{$basePath}/{$relativePath}", $stubName);
        }
    }

    private function makeFileFromStub(string $name, string $path, string $stubName): void
    {
        $stub = File::get(base_path("stubs/source/{$stubName}.stub"));

        $content = str_replace(
            ['{{ name }}', '{{ nameLower }}', '{{ nameUpper }}'],
            [$name, Str::lower($name), Str::upper($name)],
            $stub
        );

        File::put($path, $content);

        $this->line("File created: {$path}");
    }
}
