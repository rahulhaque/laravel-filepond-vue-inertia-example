# Laravel FilePond Jetstream Vue Inertia Example

Demo project to show how to use Rahul Haque's [Laravel Filepond](https://github.com/rahulhaque/laravel-filepond) package in Laravel Jetstream Vue Inertia project. Jump to the implementation directly by opening below files or install locally to tinker with various options. 
- [./resources/js/Pages/Dashboard.vue](resources/js/Pages/Dashboard.vue) - frontend logic.
- [./app/Http/Middleware/HandleInertiaRequests.php](app/Http/Middleware/HandleInertiaRequests.php) - csrf token share.
- [./routes/web.php](routes/web.php) - backend logic.

## Requirements

- PHP8 or greater
- MySQL
- Composer
- Npm

## Installation

```bash
# Clone or download the repo
git clone https://github.com/rahulhaque/laravel-filepond-vue-inertia-example

# `cd` into the project directory
cd laravel-filepond-vue-inertia-example

# Copy `.env.example` file as `.env`
# Edit `.env` variables if required
# Create a database named `laravel_filepond_vue_inertia_example`
cp .env.example .env

# Install packages
composer install

# Generate application key file
php artisan key:generate

# Install npm packages
npm install && npm run dev

# Migrate the database
php artisan migrate --seed

# Start the server
php artisan serve
```

## Authentication

Visit [http://localhost:8000/login](http://localhost:8000/login)

Default user info.
- Email: admin@email.com
- Password: password

## References

- [FilePond](https://pqina.nl/filepond/)
- [Laravel Filepond](https://github.com/rahulhaque/laravel-filepond)

## Contributing

Any code improvement contribution or suggestions are welcome.

## Credits

-   [Rahul Haque](https://github.com/rahulhaque)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
