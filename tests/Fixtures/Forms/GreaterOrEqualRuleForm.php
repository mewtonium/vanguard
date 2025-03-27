<?php

namespace Mewtonium\Vanguard\Tests\Fixtures\Forms;

use Mewtonium\Vanguard\Vanguard;
use Mewtonium\Vanguard\Rules\GreaterOrEqual;

class GreaterOrEqualRuleForm {
    use Vanguard;

    public function __construct(
        #[GreaterOrEqual(10)]
        protected int $num1,

        #[GreaterOrEqual(10)]
        protected int $num2,

        #[GreaterOrEqual(10, message: 'You must pick a number greater or equal to 10')]
        protected int $num3,

        #[GreaterOrEqual('2025-01-01')]
        protected string $date,
    ) {
        //
    }
}
