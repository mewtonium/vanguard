<?php

namespace Mewtonium\Vanguard\Tests\Fixtures\Forms;

use Mewtonium\Vanguard\Vanguard;
use Mewtonium\Vanguard\Rules\LessOrEqual;

class LessOrEqualRuleForm {
    use Vanguard;

    public function __construct(
        #[LessOrEqual(10)]
        protected int $num1,

        #[LessOrEqual(10)]
        protected int $num2,

        #[LessOrEqual(10, message: 'You must pick a number less or equal to 10')]
        protected int $num3,

        #[LessOrEqual('2025-01-01')]
        protected string $date,
    ) {
        //
    }
}
