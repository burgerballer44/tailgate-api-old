<?php

namespace TailgateApi\Validators;

use Respect\Validation\Exceptions\ExceptionInterface;
use Tailgate\Application\Validator\ValidatorInterface;

/**
 * We need a way to validate commands before they are run by their handlers.
 * Sure, we could have each handler have a validator dependency by I think it is better to fail earlier.
 * Implementations of this validator should be ran before the handler executes the command.
 */
abstract class AbstractRespectValidator implements ValidatorInterface
{
    /**
     * key value pairs
     * key is the name of the field
     * value is the Validation chain
     */
    protected $rules = [];

    /**
     * key value pairs
     * key is the name of the field
     * value is the error message
     */
    protected $errors = [];

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
    public function assert($command) : bool
    {
        $this->addRules($command);

        foreach ($this->rules as $key => $validator) {
            try {
                $method = $this->getMethodNameFromField($key);
                $validator->assert($command->$method());
            } catch (ExceptionInterface $e) {
                $this->errors[$key] = $e->getMessages();
            }
        }

        return empty($this->errors());
    }

    /**
     * [errors description]
     * @return [type] [description]
     */
    public function errors() : array
    {
        return $this->errors;
    }

    /**
     * [getMethodNameFromField description]
     * @param  [type] $field [description]
     * @return [type]        [description]
     */
    private function getMethodNameFromField($field)
    {
        return 'get' . str_replace('_', '', ucwords($field, '_'));
    }
}
