# Laravel Validator Extension

[![Build Status](https://travis-ci.org/kohkimakimoto/LaravelValidatorExtension.svg?branch=master)](https://travis-ci.org/kohkimakimoto/LaravelValidatorExtension)
[![Coverage Status](https://coveralls.io/repos/kohkimakimoto/LaravelValidatorExtension/badge.png?branch=master)](https://coveralls.io/r/kohkimakimoto/LaravelValidatorExtension?branch=master)

An extension for [Laravel4](http://laravel.com/) validator.

* Support to define validation rules in a specific class.
* Provide another syntax to define validation rules.
* Filter input values before and after validation.

Look at [usage](#usage) to get more detail.

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

Add `BaseValidator` alias to `aliases` configuration in `app/config/app.php`.

```php
'aliases' => array(
    ...
    'BaseValidator' => 'Kohkimakimoto\ValidatorExtension\Validator',
),
```

Add a path to laravel class loader in `app/start/global.php`.

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

## Usage

* [Define validation rules](#define-validation-rules)
* [Filters](#filters)
* [Custom validation rules](#custom-validation-rules)
* [Custom methods](#custom-methods)

### Define validation rules

You can define validation rules in a validator class. If you added a path to autoload and class loader configuration at the installation steps, you can define the validator class in the `app/validators` directory.

```php
// app/validators/BlogValidator.php
class BlogValidator extends BaseValidator
{
    protected function configure()
    {
        $this
            ->rule('title', 'required', 'Title is required.')
            ->rule('title', 'max:100', 'Title must not be greater than 100 characters.')
            ->rule('body', 'pass')
            ;
    }
}
```

You can use `$this->rule` method to add a validation rule. The third argument is optional to customize a error message.

The validator class is used as the below.

```php
$validator = BlogValidator::make(Input::all());
if ($validator->fails()) {
    return Redirect::back()->withInput(Input::all())->withErrors($validator);
}

// Get only validated data.
$data = $validator->onlyValidData();
```

### Filters

You can filter input values before and after validation.

```php
class BlogValidator extends BaseValidator
{
    protected function configure()
    {
        $this->beforeFilter(function($validator){
            // your code
        });

        $this->afterFilter(function($validator){
            // Modify title after validation.
            $title = $validator->title;
            $title .= " created by kohki";
            $validator->title = $title;
        });
    }
}
```

### Custom validation rules

You can define custom validation rules in the class.

```php
class BlogValidator extends BaseValidator
{
    protected function configure()
    {
        $this
            ->rule('title', 'required', 'Title is required.')
            ->rule('title', 'max:100', 'Title must not be greater than 100 characters.')
            ->rule('body', 'foo', 'Body must be foo only!')
            ;
    }

    protected function validateFoo($attribute, $value, $parameters)
    {
        return $value == 'foo';
    }
}
```

### Custom methods

The validator can be used as a value object. So you can append some custom methods to manipulate data stored in it.

```php
class BlogValidator extends BaseValidator
{
    public function getTitleOrDefault() {
        if ($this->title === null) {
            return "Default title";
        } else {
            return $this->title;
        }
    }
}
```

## LICENSE

The MIT License

## Author

Kohki Makimoto <kohki.makimoto@gmail.com>
