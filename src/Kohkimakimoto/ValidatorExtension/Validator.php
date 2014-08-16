<?php
namespace Kohkimakimoto\ValidatorExtension;

use Closure;
use Symfony\Component\Translation\TranslatorInterface;
use Illuminate\Validation\Validator as IlluminateValidator;
use Illuminate\Support\MessageBag;

/**
 * Custom Validator
 */
abstract class Validator extends IlluminateValidator
{
    protected static $defaultTranslator;

    protected $validAttributes = array();

    protected $beforeFilter;

    protected $afterFilter;

    protected $options;

    public static function make($data, $options = array())
    {
        $instance = new static(static::$defaultTranslator, $data, array());
        $instance->options = $options;
        $instance->configure();
        return $instance;
    }

    public static function setDefaultTranslator(TranslatorInterface $defaultTranslator)
    {
        static::$defaultTranslator = $defaultTranslator;
    }

    public static function getDefaultTranslator()
    {
        return static::$defaultTranslator;
    }

    protected abstract function configure();

    public function rule($attribute, $rule, $message = null)
    {
        $this->mergeRules($attribute, $rule);
        if ($message !== null) {
            list($rule, $parameters) = $this->parseRule($rule);
            $lowerRule = snake_case($rule);
            $this->setCustomMessages(
                array("${attribute}.${lowerRule}" => $message)
            );

        }
        return $this;
    }

    public function beforeFilter(Closure $beforeFilter)
    {
        $this->beforeFilter = $beforeFilter;
    }

    public function afterFilter(Closure $afterFilter)
    {
        $this->afterFilter = $afterFilter;
    }

    public function passes()
    {
        if (!is_null($this->beforeFilter)) {
            if (call_user_func($this->beforeFilter, $this) === false) {
                $this->messages = new MessageBag;
                return false;
            }
        }

        $ret = parent::passes();

        if ($ret === true) {
            if (!is_null($this->afterFilter)) {
                if (call_user_func($this->afterFilter, $this) === false) {
                    return false;
                }
            }
        }
        return $ret;
    }

    public function get($key, $default = null)
    {
        return array_get($this->data, $key, $default);
    }

    public function set($key, $value)
    {
        return $this->data[$key] = $value;
    }

    public function validData()
    {
        return array_only($this->data, array_keys($this->validAttributes));
    }

    protected function validate($attribute, $rule)
    {
        parent::validate($attribute, $rule);
        $this->validAttributes[$attribute] = true;
    }

    protected function validatePass($attribute, $value)
    {
        return true;
    }

    public function __call($method, $parameters)
    {
        return parent::__call($method, $parameters);
    }
}
