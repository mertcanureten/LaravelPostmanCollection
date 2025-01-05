<?php

namespace Mertcanureten\LaravelPostmanCollection\Console;

use Illuminate\Console\Command;
use Mertcanureten\LaravelPostmanCollection\PostmanCollectionGenerator;

class ExportPostmanCollection extends Command
{
    protected $signature = 'postman:export 
        {--output=postman_collection.json : Output file name}
        {--name= : Collection name}
        {--auth-type=bearer : Authentication type (bearer|basic)}';

    protected $description = 'Export Laravel API routes to a Postman collection';

    public function handle()
    {
        try {
            $outputFile = $this->option('output');
            $generator = new PostmanCollectionGenerator();

            if ($this->option('name')) {
                config(['app.name' => $this->option('name')]);
            }

            $collection = $generator->generate();
            
            if (!file_put_contents($outputFile, json_encode($collection, JSON_PRETTY_PRINT))) {
                throw new \RuntimeException("Failed to write to file: {$outputFile}");
            }

            $this->info("✓ Postman collection exported successfully to {$outputFile}");
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }
    }
}