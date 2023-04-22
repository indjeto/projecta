<?php

namespace App\Exception;

class ValidationViolationException extends \Exception
{
    public function __construct(
        protected \Traversable $violations,
        ?Throwable $previous = null
    ) {
        parent::__construct("Violation validation rules", -1, $previous);
    }

    public function getViolations()
    {
        return $this->violations;
    }
}