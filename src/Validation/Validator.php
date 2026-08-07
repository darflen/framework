<?php

declare(strict_types=1);

namespace Darflen\Framework\Validation;

use Darflen\Framework\Validation\Interfaces\RuleInterface;
use ReflectionClass;

class Validator
{
    private array $errors = [];

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
        $rule = (new ReflectionClass($rule))->getShortName();
        $this->errors[$field][] = $rule;
    }

    public function getErrors(): array
    {
        return $this->errors;
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
