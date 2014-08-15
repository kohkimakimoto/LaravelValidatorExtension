# Laravel Validator Extension

An extension for Laravel4 validator.

# Installation

Add dependency in `composer.json`

```json
"require-dev": {
    "kohkimakimoto/laravel-validator-extension": "0.*"
}
```

Run `composer upadte` command.

```
$ composer update
```

Add Alias to `aliases` configuration in `app/config/app.php`

```php
'aliases' => array(
    ...
    'BaseValidator' => 'Kohkimakimoto\ValidatorExtension\ValidatorSchema',
),
```

Add a path to Laravel class loader in `app/start/global.php`

```php
ClassLoader::addDirectories(array(
    ...
    app_path().'/validators',
));
```



