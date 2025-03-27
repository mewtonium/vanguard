<?php

namespace Mewtonium\Vanguard\Tests\Fixtures\Forms;

use Mewtonium\Vanguard\Vanguard;
use Mewtonium\Vanguard\Rules\LessThan;

class LessThanRuleForm {
    use Vanguard;

    public function __construct(
        #[LessThan(10)]
        protected int $num1,

        #[LessThan(10)]
        protected int $num2,

        #[LessThan(10, message: 'You must pick a number less than 10')]
        protected int $num3,

        #[LessThan('2025-01-01')]
        protected string $date,
    ) {
        //
    }
}
