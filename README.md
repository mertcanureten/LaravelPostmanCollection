# Laravel Postman Collection

This is a Laravel package that allows you to generate a Postman collection from your Laravel API routes and export it for easy import into the Postman application.

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

### Options

- `--output`: Specify the output file name for the Postman collection (default: `postman_collection.json`).

The generated Postman collection will now include endpoint parameters with their respective details.

## Example

To export the collection with a custom file name:

```bash
php artisan postman:export --output=my_custom_collection.json
```

## Importing into Postman

After running the command, you will find the generated `postman_collection.json` file in your project root. You can import this file into Postman using the following steps:

1. Open Postman.
2. Click on "Import" in the top-left corner.
3. Select the `postman_collection.json` file.
4. Click "Import" to add the collection to your Postman workspace.

## License

This project is licensed under the MIT License.
