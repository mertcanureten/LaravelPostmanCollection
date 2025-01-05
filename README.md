# Laravel Postman Collection

This is a Laravel package that allows you to generate a Postman collection from your Laravel API routes and export it for easy import into the Postman application.

## Features

- 🔄 Automatically generates Postman collection from Laravel API routes
- 🔑 Built-in authentication support (Bearer Token, Basic Auth)
- 📁 Groups API endpoints by their URL prefix for better organization
- 📝 Includes route descriptions from PHPDoc comments
- 🔍 Dynamic model parameter detection
- 🎯 Default headers for JSON API requests
- ⚡ Easy to install and use

## Installation

You can install this package via Composer:

```bash
composer require mertcanureten/laravel-postman-collection
```

## Usage

Once the package is installed, you can use the following Artisan command to export your API routes to a Postman collection:

```bash
php artisan postman:export
```

### Command Options

- `--output`: Specify the output file name (default: `postman_collection.json`)
- `--name`: Set a custom name for the collection
- `--auth-type`: Specify the authentication type (bearer|basic)

### Examples

Basic usage:
```bash
php artisan postman:export
```

With custom options:
```bash
php artisan postman:export --output=api.json --name="My API" --auth-type=bearer
```

## Generated Collection Features

The generated Postman collection includes:

- Grouped endpoints by URL prefix
- Authentication settings (Bearer Token by default)
- Default headers for JSON API requests:
  - `Accept: application/json`
  - `Content-Type: application/json`
- Route descriptions from PHPDoc comments
- Parameter details with types and descriptions
- Pre-configured JSON request bodies

## Importing into Postman

After generating the collection, follow these steps to import it into Postman:

1. Open Postman
2. Click on "Import" in the top-left corner
3. Select the generated JSON file
4. Click "Import" to add the collection to your workspace

### Authentication Setup

The collection comes with pre-configured Bearer Token authentication. To use it:

1. Create an environment in Postman
2. Add a variable named `auth_token`
3. Set your API token as the variable value

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.
