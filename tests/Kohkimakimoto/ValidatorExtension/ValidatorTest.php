<?php
namespace Test\Kohkimakimoto\ValidatorExtension;

use Kohkimakimoto\ValidatorExtension\Validator;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        Validator::setDefaultTranslator($this->getRealTranslator());
    }

    public function testAddRuleAndRunPassesTrue()
    {
        $v = Test01Validator::make(array('foo' => 'bar'));
        $v->rule('foo', 'required');
        $this->assertTrue($v->passes());

        $v = Test02Validator::make(array('foo' => 'bar'));
        $this->assertTrue($v->passes());
    }

    public function testAddRuleAndRunPassesFalse()
    {
        $v = Test01Validator::make(array('foo' => null));
        $v->rule('foo', 'required');
        $this->assertFalse($v->passes());

        $v = Test02Validator::make(array('foo' => null));
        $this->assertFalse($v->passes());
    }

    public function testFilters()
    {
        $v = Test01Validator::make(array('foo' => 'aaa', 'bar' => 'bbb'));
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

    public function testFilters2()
    {
        $v = Test03Validator::make(array('foo' => 'aaa', 'bar' => 'bbb'));
        $this->assertTrue($v->passes());
        $this->assertEquals("aaafoo", $v->get('foo'));
        $this->assertEquals("bbbbar", $v->get('bar'));
    }

    public function testCustomRules()
    {
        $v = Test04Validator::make(array('title' => 'aaa', 'body' => 'bbb'));
        $this->assertFalse($v->passes());
        $this->assertEquals('Body must be foo only!', $v->getMessageBag()->first('body'));

        $v = Test04Validator::make(array('title' => 'aaa', 'body' => 'foo'));
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


class Test01Validator extends Validator
{
    protected function configure()
    {
    }
}

class Test02Validator extends Validator
{
    protected function configure()
    {
        $this->rule('foo', 'required');
    }
}

class Test03Validator extends Validator
{
    protected function configure()
    {
        $this->rule('foo', 'required');
        $this->rule('bar', 'required');

        $this->beforeFilter(function($v){
            $foo = $v->get('foo');
            $foo .= "foo";
            $v->set('foo', $foo);
        });

        $this->afterFilter(function($v){
            $bar = $v->get('bar');
            $bar .= "bar";
            $v->set('bar', $bar);
        });
    }
}

class Test04Validator extends Validator
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