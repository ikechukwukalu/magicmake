<?php

namespace Ikechukwukalu\Magicmake\Generation;

use Illuminate\Filesystem\Filesystem;
use RuntimeException;

class InitPlanFactory
{
    /** @var string */
    private $basePath;

    /** @var string */
    private $stubPath;

    /** @var Filesystem */
    private $files;

    public function __construct($basePath, $stubPath, ?Filesystem $files = null)
    {
        $this->basePath = rtrim($basePath, '/');
        $this->stubPath = rtrim($stubPath, '/');
        $this->files = $files ?: new Filesystem;
    }

    public function make()
    {
        $plan = new GenerationPlan($this->basePath);
        $mappings = [
            'app/Actions' => 'app/Actions',
            'app/Contracts' => 'app/Contracts',
            'app/Enums' => 'app/Enums',
            'app/Events' => 'app/Events',
            'app/Exceptions' => 'app/Exceptions',
            'app/Facades' => 'app/Facades',
            'app/Http' => 'app/Http',
            'app/Controllers' => 'app/Http/Controllers',
            'app/AuthControllers' => 'app/Http/Controllers/Auth',
            'app/Middleware' => 'app/Http/Middleware',
            'app/Requests' => 'app/Http/Requests',
            'app/AuthRequests' => 'app/Http/Requests/Auth',
            'app/Listeners' => 'app/Listeners',
            'app/Models' => 'app/Models',
            'app/ModelsScopes' => 'app/Models/Scopes',
            'app/Notifications' => 'app/Notifications',
            'app/Providers' => 'app/Providers',
            'app/Repositories' => 'app/Repositories',
            'app/Rules' => 'app/Rules',
            'app/Services' => 'app/Services',
            'app/AuthServices' => 'app/Services/Auth',
            'app/Traits' => 'app/Traits',
            'config' => 'config',
            'database/factories' => 'database/factories',
            'database/migrations' => 'database/migrations',
            'lang/en' => 'lang/en',
            'routes/app' => 'routes/app',
            'tests/Feature' => 'tests/Feature',
            'resources/views/layouts' => 'resources/views/layouts',
            'resources/views/passwords' => 'resources/views/passwords',
            'resources/views/socialite' => 'resources/views/socialite',
            'resources/views/twofactor' => 'resources/views/twofactor',
        ];

        foreach ($mappings as $source => $destination) {
            $this->addDirectory($plan, $source, $destination);
        }

        $plan->addUniqueAppend($this->basePath.'/routes/web.php', $this->readStub('routes/web.stub'));
        $plan->addUniqueAppend($this->basePath.'/routes/api.php', $this->readStub('routes/api.stub'));

        return $plan;
    }

    private function addDirectory(GenerationPlan $plan, $source, $destination)
    {
        $sourcePath = $this->stubPath.'/'.$source;
        if (! is_dir($sourcePath)) {
            throw new RuntimeException("Initialization stub directory [{$sourcePath}] does not exist.");
        }

        foreach ($this->files->allFiles($sourcePath) as $file) {
            $relative = ltrim(str_replace($sourcePath, '', $file->getPathname()), '/');
            $relative = preg_replace('/\.stub$/', '.php', $relative);
            $plan->addFile(
                $this->basePath.'/'.$destination.'/'.$relative,
                (string) file_get_contents($file->getPathname())
            );
        }
    }

    private function readStub($relative)
    {
        $path = $this->stubPath.'/'.$relative;
        if (! is_file($path)) {
            throw new RuntimeException("Initialization stub [{$path}] does not exist.");
        }

        return (string) file_get_contents($path);
    }
}
