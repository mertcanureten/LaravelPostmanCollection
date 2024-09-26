<?php

namespace Mertcanureten\LaravelPostmanCollection;

use Illuminate\Support\Facades\Route;
use ReflectionMethod;

class PostmanCollectionGenerator
{
    public function generate()
    {
        $routes = Route::getRoutes();
        $apiRoutes = collect($routes)->filter(function ($route) {
            return in_array('api', $route->gatherMiddleware());
        });
        $collection = [
            'info' => [
                'name' => 'Laravel API',
                'description' => 'Generated Postman Collection from Laravel API routes',
                'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json'
            ],
            'item' => []
        ];

        foreach ($apiRoutes as $route) {
            $action = $route->getAction();
            $controller = $action['controller'] ?? null;
            $description = $this->getDescription($controller, $action['as'] ?? null);

            $collection['item'][] = [
                'name' => $route->getName() ?? 'Unnamed Route',
                'request' => [
                    'method' => $route->methods()[0],
                    'header' => [],
                    'body' => [],
                    'url' => [
                        'raw' => url($route->uri()),
                        'host' => [parse_url(url('/'), PHP_URL_HOST)],
                        'path' => explode('/', trim($route->uri(), '/')),
                    ],
                    'description' => $description,
                ],
                'param' => $this->getParameters($route->parameters(), $controller),
            ];
        }

        return $collection;
    }

    private function getParameters($params, $controller)
    {
        $paramArray = [];
        $actionMethod = explode('@', $controller)[1];

        // User modelini kullanarak $fillable değerlerini alıyoruz
        $fillableFields = (new \App\Models\User())->getFillable();

        foreach ($params as $key => $value) {
            // Eğer parametre request'ten geliyorsa
            if (in_array($key, $fillableFields)) {
                $paramArray[] = [
                    'key' => $key,
                    'value' => $value,
                    'description' => "The {$key} field for the user.",
                    'type' => $this->getParamType($controller, $actionMethod, $key),
                ];
            }
        }

        return $paramArray;
    }

    private function getParamType($controller, $methodName, $paramName)
    {
        if (!$controller) return 'string'; // Default type if no controller found

        $controllerClass = explode('@', $controller)[0];
        $reflectionMethod = new ReflectionMethod($controllerClass, $methodName);

        // Loop through parameters to find type hinting
        foreach ($reflectionMethod->getParameters() as $parameter) {
            if ($parameter->getName() === $paramName) {
                $type = $parameter->getType();
                return $type ? $type->getName() : 'mixed'; // Return the type name or 'mixed' if no type
            }
        }

        return 'string'; // Fallback type
    }

    private function getDescription($controller, $routeName)
    {
        if (!$controller) return 'No description available.';

        list($controllerClass, $methodName) = explode('@', $controller);
        $reflection = new ReflectionMethod($controllerClass, $methodName);

        // Get the doc comment from the method
        $docComment = $reflection->getDocComment();
        
        // Extract description from the comment (this is a simplistic approach)
        preg_match('/\*\s+(.*)/', $docComment, $matches);

        return $matches[1] ?? 'No description available.';
    }
}