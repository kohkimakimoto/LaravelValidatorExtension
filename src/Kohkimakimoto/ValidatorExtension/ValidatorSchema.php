<?php
namespace Kohkimakimoto\ValidatorExtension;

use Illuminate\Support\Facades\App;

abstract class ValidatorSchema
{
    protected $data;
    protected $options;

    public static function make($data, $options = array())
    {
        return with(new static())->makeupValidator($data, $options);
    }

    protected function makeupValidator($data, $options)
    {
        $validator = $this->createValidator($data);
        $validator->setSchema($this);
        $this->data = $data;
        $this->options = $options;
        $this->configure($validator);

        return $validator;
    }

    protected function createValidator($data)
    {
        return new Validator(App::make('translator'), $data, []);
    }

    protected abstract function configure($validator);
}