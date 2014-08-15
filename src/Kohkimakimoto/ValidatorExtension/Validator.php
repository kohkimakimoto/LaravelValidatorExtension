<?php
namespace Kohkimakimoto\ValidatorExtension;

use Closure;
use Illuminate\Validation\Validator as IlluminateValidator;
use Illuminate\Support\MessageBag;

/**
 * Custom Validator
 */
class Validator extends IlluminateValidator
{
    protected $validAttributes = array();

    protected $beforeFilter;

    protected $afterFilter;

    protected $schema;

    public function setSchema($schema)
    {
        $this->schema = $schema;
    }

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
        if (method_exists($this->schema, $method)) {
            return call_user_func_array(array($this->schema, $method), $parameters);
        }

        return parent::__call($method, $parameters);
    }
}
