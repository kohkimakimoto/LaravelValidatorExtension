<?php
namespace Test\Kohkimakimoto\ValidatorExtension;

use Kohkimakimoto\ValidatorExtension\Validator;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testAddRuleAndRunPassesTrue()
    {
        $v = new Validator(
            $this->getRealTranslator(), 
            array('foo' => 'bar'), 
            array());
        $v->rule('foo', 'required');

        $this->assertTrue($v->passes());
    }

    public function testAddRuleAndRunPassesFalse()
    {
        $v = new Validator(
            $this->getRealTranslator(), 
            array('foo' => null), 
            array());
        $v->rule('foo', 'required');

        $this->assertFalse($v->passes());
    }

    public function testFilters()
    {
        $v = new Validator(
            $this->getRealTranslator(), 
            array('foo' => 'aaa', 'bar' => 'bbb'), 
            array());
        $v->rule('foo', 'required');
        $v->rule('bar', 'required');
        $v->beforeFilter(function($v){
            $foo = $v->get('foo');
            $foo .= "foo";
            $v->set('foo', $foo);
        });

        $v->afterFilter(function($v){
            $bar = $v->get('bar');
            $bar .= "bar";
            $v->set('bar', $bar);
        });

        $this->assertTrue($v->passes());
        $this->assertEquals("aaafoo", $v->get('foo'));
        $this->assertEquals("bbbbar", $v->get('bar'));
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