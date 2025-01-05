<?php

namespace Mertcanureten\LaravelPostmanCollection;

use Illuminate\Support\Facades\Route;
use ReflectionMethod;

class PostmanCollectionGenerator
{
    private $config;
    
    public function __construct()
    {
        $this->config = [
            'auth' => [
                'type' => 'bearer',
                'bearer' => ['token' => '{{auth_token}}']
            ]
        ];
    }

    public function generate()
    {
        try {
            $apiRoutes = collect(Route::getRoutes())->filter(function ($route) {
                return in_array('api', $route->gatherMiddleware());
            });

            $collection = [
                'info' => [
                    'name' => config('app.name', 'Laravel') . ' API',
                    'description' => 'Generated Postman Collection from Laravel API routes',
                    'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json'
                ],
                'auth' => $this->config['auth'],
                'item' => $this->groupRoutesByPrefix($apiRoutes)
            ];

            return $collection;
        } catch (\Exception $e) {
            throw new \RuntimeException("Failed to generate collection: " . $e->getMessage());
        }
    }

    private function groupRoutesByPrefix($routes)
    {
        $groups = [];
        
        foreach ($routes as $route) {
            $prefix = explode('/', $route->uri())[0] ?? 'general';
            
            if (!isset($groups[$prefix])) {
                $groups[$prefix] = [
                    'name' => ucfirst($prefix),
                    'item' => []
                ];
            }

            $groups[$prefix]['item'][] = $this->createRequestItem($route);
        }

        return array_values($groups);
    }

    private function createRequestItem($route)
    {
        $action = $route->getAction();
        $controller = $action['controller'] ?? null;
        $description = $this->getDescription($controller, $action['as'] ?? null);

        return [
            'name' => $route->getName() ?? $route->uri(),
            'request' => [
                'method' => $route->methods()[0],
                'header' => [
                    [
                        'key' => 'Accept',
                        'value' => 'application/json'
                    ],
                    [
                        'key' => 'Content-Type',
                        'value' => 'application/json'
                    ]
                ],
                'body' => [
                    'mode' => 'raw',
                    'raw' => '{}',
                    'options' => [
                        'raw' => [
                            'language' => 'json'
                        ]
                    ]
                ],
                'url' => [
                    'raw' => url($route->uri()),
                    'host' => [parse_url(url('/'), PHP_URL_HOST)],
                    'path' => explode('/', trim($route->uri(), '/')),
                ],
                'description' => $description,
            ],
            'response' => []
        ];
    }

    private function getParameters($params, $controller)
    {
        try {
            $paramArray = [];
            if (!$controller) return $paramArray;

            list($controllerClass, $actionMethod) = explode('@', $controller);
            
            // Controller sınıfından model bilgisini almaya çalış
            $controllerInstance = app($controllerClass);
            $model = property_exists($controllerInstance, 'model') ? 
                     app($controllerInstance->model) : null;

            if ($model) {
                $fillableFields = $model->getFillable();
                
                foreach ($params as $key => $value) {
                    if (in_array($key, $fillableFields)) {
                        $paramArray[] = [
                            'key' => $key,
                            'value' => $value,
                            'description' => "The {$key} field.",
                            'type' => $this->getParamType($controller, $actionMethod, $key),
                        ];
                    }
                }
            }

            return $paramArray;
        } catch (\Exception $e) {
            return [];
        }
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