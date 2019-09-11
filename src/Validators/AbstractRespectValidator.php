<?php

namespace TailgateApi\Validators;

/**
 * We need a way to validate commands before they are run by their handlers.
 * Sure, we could have each handler have a validator dependency by I think
 * it is better to fail earlier.
 * Implementations of this validator should be ran before the handler
 * executes the command.
 */
abstract class AbstractRespectValidator
{
    /**
     * key value pairs
     * key is the name of the field
     * value is the Validation chain
     */
    protected $rules = [];

    /**
     * key value pairs
     * key is the name of the validation
     * value is the message
     */
    protected $messages = [];

    /**
     * key value pairs
     * key is the name of the field
     * value is the error message
     */
    protected $errors = [];

    /**
     * [__construct description]
     */
    public function __construct()
    {
        $this->initMessages();
    }

    /**
     * return an arr of any messages that you want to add or change
     * @return [type] [description]
     */
    abstract protected function messageOverWrites() : array;

    /**
     * add validations rules to the fields
     * @param [type] $command [description]
     */
    abstract protected function addRules($command);

    /**
     * [assert description]
     * @param  [type] $command [description]
     * @return [type]          [description]
     */
    public function assert($command)
    {
        $this->addRules($command);
        $this->assertEachField($command);

        // if there are errors then we have a problem
        if (!empty($this->errors)) {
            return false;
        }

        return true;
    }

    /**
     * [getMethodNameFromField description]
     * @param  [type] $field [description]
     * @return [type]        [description]
     */
    protected function getMethodNameFromField($field)
    {
        return 'get' . str_replace('_', '', ucwords($field, '_'));
    }

    /**
     * [assertEachField description]
     * @param  [type] $command [description]
     * @return [type]          [description]
     */
    protected function assertEachField($command)
    {
        foreach ($this->rules as $field => $validator) {
            $method = $this->getMethodNameFromField($field);

            try {
                $validator->assert($command->$method());
            } catch (\Throwable $e) {
                // $this->errors[$field] = $e->getMessages();
                // $this->errors[$field] = $e->findMessages($this->messages);
                $this->errors[$field] = array_filter($e->findMessages($this->messages), function ($error) {
                    return !empty($error);
                });
                // $this->errors[$field] = $e->getFullMessage();
            }
        }
    }

    /**
     * [initMessages description]
     * @return [type] [description]
     */
    private function initMessages()
    {
        $this->messages = array_merge([
            'notEmpty'       => '{{name}} is required.',
            'alnum'          => '{{name}} must only contain alphanumeric characters.',
            'noWhitespace'   => '{{name}} must not contain empty spaces.',
            'length'         => '{{name}} must be between {{minValue}} and {{maxValue}} characters.',
            'email'          => 'Please make sure you typed a correct email address.',
        ], $this->messageOverWrites());
    }

    /**
     * [errors description]
     * @return [type] [description]
     */
    public function errors()
    {
        return $this->errors;
    }
}
