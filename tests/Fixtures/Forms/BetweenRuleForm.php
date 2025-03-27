<?php

namespace Mewtonium\Vanguard\Tests\Fixtures\Forms;

use Mewtonium\Vanguard\Vanguard;
use Mewtonium\Vanguard\Rules\Between;

class BetweenRuleForm {
    use Vanguard;

    public function __construct(
        #[Between(1, 10)]
        protected int $num1,

        #[Between(1, 10, message: 'You must pick a number between 1 and 10')]
        protected int $num2,

        #[Between('2025-01-01', '2026-01-01')]
        protected string $date,
    ) {
        //
    }
}
