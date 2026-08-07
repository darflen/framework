<?php

declare(strict_types=1);

namespace Darflen\Framework\Validation;

use Darflen\Framework\Validation\Interfaces\ParametersAwareInterface;
use Darflen\Framework\Validation\Interfaces\RuleInterface;
use ReflectionClass;

class Validator
{
    private array $errors = [];

    private array $parameters = [];

    /**
     * Validate inputs using rules and strict mode.
     *
     * @param  array<string,mixed> $inputs
     * @param  array<string,RuleInterface[]> $rules
     * @param  array<string,bool> $stricts
     * @return self
     */
    public function validateInputs(array $inputs, array $rules, array $stricts = []): self
    {
        $this->errors = [];
        foreach ($rules as $field => $inputRules) {
            $input = $inputs[$field] ?? null;
            $isInputStrict = $stricts[$field] ?? false;
            foreach ($inputRules as $inputRule) {
                if (!$inputRule->validate($input)) {
                    $this->addError($field, $inputRule);
                    if ($isInputStrict) {
                        break;
                    }
                }
            }
        }
        return $this;
    }

    protected function addError(string $field, RuleInterface $rule): void
    {
        $name = (new ReflectionClass($rule))->getShortName();
        $parameters = $rule instanceof ParametersAwareInterface ? $rule->getParameters() : [];
        $this->errors[$field][] = $name;
        $this->parameters[$field][$name] = $parameters;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get all the errors parameters from rule
     *
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function didPass(): bool
    {
        return $this->errors === [];
    }

    public function didFail(): bool
    {
        return !$this->didPass();
    }
}
