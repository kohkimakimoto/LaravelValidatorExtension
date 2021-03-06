<?php
namespace Kohkimakimoto\ValidatorExtension;

use Closure;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\File\File;
use Illuminate\Validation\Validator as IlluminateValidator;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Contracts\JsonableInterface;
use Illuminate\Support\Contracts\ArrayableInterface;

/**
 * Custom Validator
 */
abstract class Validator extends IlluminateValidator implements ArrayableInterface, JsonableInterface
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
        $this->messages = new MessageBag;

        if (!is_null($this->beforeFilter)) {
            if (call_user_func($this->beforeFilter, $this) === false) {
                return false;
            }
        }

        // We'll spin through each rule, validating the attributes attached to that
        // rule. Any error messages will be added to the containers with each of
        // the other error messages, returning true if we don't have messages.
        foreach ($this->rules as $attribute => $rules)
        {
            foreach ($rules as $rule)
            {
                $this->validate($attribute, $rule);
            }
        }

        $ret = count($this->messages->all()) === 0;

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
        if (!is_null($value = $this->getValue($key))) {
            return $value;
        } else {
            return $default;
        }
    }

    public function set($key, $value)
    {
        if ($value instanceof File) {
            $this->files[$key] = $value;
            unset($data[$key]);
        }
        $this->data[$key] = $value;

        return $this;
    }

    public function getOption($key, $default = null)
    {
        return isset($this->options[$key]) ? $this->options[$key] : $default;
    }

    public function setOption($key, $value)
    {
        $this->options[$key] = $value;
        return $this;
    }

    protected function validate($attribute, $rule)
    {
        parent::validate($attribute, $rule);
        $this->validAttributes[$attribute] = true;
    }

    public function only($keys)
    {
        return array_only($this->data, $keys);
    }

    /**
     * Returns only validated data.
     * @return [type] [description]
     */
    public function onlyValidData()
    {
        return array_only($this->data, array_keys($this->validAttributes));
    }

    /**
     * Additional validation rule that always passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    protected function validatePass($attribute, $value)
    {
        return true;
    }

    /**
     * Convert the model instance to JSON.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    public function __get($key)
    {
        return $this->get($key);
    }

    public function __set($key, $value)
    {
        $this->set($key, $value);
    }
}
