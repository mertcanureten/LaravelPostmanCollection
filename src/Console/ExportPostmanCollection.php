<?php

namespace Mertcanureten\LaravelPostmanCollection\Console;

use Illuminate\Console\Command;
use Mertcanureten\LaravelPostmanCollection\PostmanCollectionGenerator;

class ExportPostmanCollection extends Command
{
    protected $signature = 'postman:export {--output=postman_collection.json}';
    protected $description = 'Export Laravel API routes to a Postman collection';

    public function handle()
    {
        $outputFile = $this->option('output');
        $generator = new PostmanCollectionGenerator();

        $collection = $generator->generate();
        file_put_contents($outputFile, json_encode($collection, JSON_PRETTY_PRINT));

        $this->info("Postman collection exported to {$outputFile}");
    }
}