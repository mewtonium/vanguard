<?php

namespace Mewtonium\Vanguard\Contracts;

interface ValidatesDates
{
    /**
     * Handles date validation.
     * 
     * @throws \Mewtonium\Vanguard\Exceptions\RuleException
     */
    public function validateDate(): bool;
}
