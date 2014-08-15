<?php
namespace Test\Kohkimakimoto\ValidatorExtensionServiceProvider;

use Kohkimakimoto\ValidatorExtension\ValidatorExtensionServiceProvider;

class ValidatorExtensionServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateProvider()
    {
        $provider = new ValidatorExtensionServiceProvider(null);
    }

}

