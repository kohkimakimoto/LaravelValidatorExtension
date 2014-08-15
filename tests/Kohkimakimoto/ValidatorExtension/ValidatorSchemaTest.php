<?php
namespace Test\Kohkimakimoto\ValidatorExtension;

use Kohkimakimoto\ValidatorExtension\ValidatorSchema;

class ValidatorSchemaTest extends \PHPUnit_Framework_TestCase
{
    public function testDefault()
    {
        ValidatorSchema::setTranslator($this->getRealTranslator());
        $v = SampleValidator::make(array());
    }

    /**
     * https://github.com/laravel/framework/blob/4.2/tests/Validation/ValidationValidatorTest.php#L1276
     */
    protected function getRealTranslator()
    {
        $trans = new \Symfony\Component\Translation\Translator('en', new \Symfony\Component\Translation\MessageSelector);
        $trans->addLoader('array', new \Symfony\Component\Translation\Loader\ArrayLoader);
        return $trans;
    }
}

class SampleValidator extends ValidatorSchema
{
    protected function configure($validator)
    {
    }
}