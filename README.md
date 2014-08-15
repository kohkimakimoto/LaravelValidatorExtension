# Laravel Validator Extension

[![Build Status](https://travis-ci.org/kohkimakimoto/LaravelValidatorExtension.svg?branch=master)](https://travis-ci.org/kohkimakimoto/LaravelValidatorExtension)
[![Coverage Status](https://coveralls.io/repos/kohkimakimoto/LaravelValidatorExtension/badge.png?branch=master)](https://coveralls.io/r/kohkimakimoto/LaravelValidatorExtension?branch=master)

An extension for Laravel4 validator.

* Support to define validation rules in a specific class.
* Provide another syntax to define validation rules.
* Filter input values before and after validation.

The following code is an example of validatior class.

```php
class BlogValidator extends BaseValidator
{
    protected function configure($validator)
    {
        $validator
            ->rule('title', 'required', 'Title is required.')
            ->rule('title', 'max:100', 'Title must not be greater than 100 characters.')
            ->rule('body', 'pass')
            ;
    }
}
```

The validation class is used as the below.

```php
// * In a controller.

$validator = BlogValidator::make(Input::all());
if ($validator->fails()) {
    return Redirect::back()->withInput(Input::all())->withErrors($validator);
}
$data = $validator->validData();
```

You can filter input values before and after validation.

```php
class BlogValidator extends BaseValidator
{
    protected function configure($validator)
    {
        $validator->beforeFilter(function($validator){
            // your code
        });

        $validator->afterFilter(function($validator){
            // Modify title after validation.
            $title = $validator->get('title');
            $title .= " created by kohki";
            $validator->set('title', $title);
        });
    }
}
```

# Installation

Add dependency in `composer.json`

```json
"require": {
    "kohkimakimoto/laravel-validator-extension": "0.*"
}
```

Run `composer upadte` command.

```
$ composer update
```

Add `ValidatorExtensionServiceProvider` provider to `providers` configuration in `app/config/app.php`.

```
'providers' => array(
    ....
    'Kohkimakimoto\ValidatorExtension\ValidatorExtensionServiceProvider',
}
```

Add `BaseValidator` Alias to `aliases` configuration in `app/config/app.php`.

```php
'aliases' => array(
    ...
    'BaseValidator' => 'Kohkimakimoto\ValidatorExtension\ValidatorSchema',
),
```

Add a path to Laravel class loader in `app/start/global.php`.

```php
ClassLoader::addDirectories(array(
    ...
    app_path().'/validators',
));
```

And add a path at `autoload` section in `composer.json`.

```json
"autoload": {
    "classmap": [
        ...
        "app/validators"
    ]
}
```

## LICENSE

The MIT License

## Author 

Kohki Makimoto <kohki.makimoto@gmail.com>