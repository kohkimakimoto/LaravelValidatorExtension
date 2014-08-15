<?php
namespace Test\Kohkimakimoto\ValidatorExtensionServiceProvider;

use Kohkimakimoto\ValidatorExtension\ValidatorExtensionServiceProvider;

class ValidatorExtensionServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testRegisterProvider()
    {
        $provider = new ValidatorExtensionServiceProvider(null);
        $provider->register();
    }
}

