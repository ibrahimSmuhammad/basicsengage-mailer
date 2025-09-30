# basicsengage-mailer

A PHP Laravel package to extend Laravel's mailer with [BasicsEngage](https://basicsengage.com) support.

## Features

- Seamless integration with Laravel's mail system
- Send emails via BasicsEngage
- Easy configuration and usage

## Installation

```bash
composer require ibrahimsmuhammad/basicsengage-laravel-mailer
```

## Configuration

1. Publish the config file (if available):

    ```bash
    php artisan vendor:publish --tag=basicsengage-mailer-config
    ```

2. Add your BasicsEngage credentials to `.env`:

    ```
        MAIL_MAILER=basicsengage
        BASICSENGAGE_API_KEY=Your Api Key
        BASICSENGAGE_API_URL="https://api.basicsengage.com/api/v0/dev/email/send"
    ```

## Usage

Use the Laravel `Mail` facade as usual. The package will route emails through BasicsEngage.

```php
use Illuminate\Support\Facades\Mail;

Mail::to('recipient@example.com')->send(new \App\Mail\YourMailable());
```

## Customization

You can customize mail settings in `config/basicsengage-mailer.php`.
```
    'mailers' => [
        'basicsengage' => [
            'transport' => 'basicsengage',
            'api_key'   => env('BASICSENGAGE_API_KEY'),
            'api_url'   => env('BASICSENGAGE_API_URL', 'https://api.basicsengage.com/api/v0/dev/email/send'),
        ],
```

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).