<?php
namespace Test\Kohkimakimoto\ValidatorExtension;

use Kohkimakimoto\ValidatorExtension\Validator;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testAddRuleAndRunPasses()
    {
        $v = new Validator(
            $this->getRealTranslator(), 
            array('foo' => 'bar'), 
            array());
        $v->rule('foo', 'required');

        $this->assertTrue($v->passes());
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