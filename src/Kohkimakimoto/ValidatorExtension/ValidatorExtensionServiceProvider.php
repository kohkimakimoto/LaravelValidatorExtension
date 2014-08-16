<?php
namespace Kohkimakimoto\ValidatorExtension;

use Illuminate\Support\ServiceProvider;

/**
 * ValidatorExtensionServiceProvider
 */
class ValidatorExtensionServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('kohkimakimoto/validator-extension');
        Validator::setDefaultTranslator($this->app->make('translator'));
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
    }
}