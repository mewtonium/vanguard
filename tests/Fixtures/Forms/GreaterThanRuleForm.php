<?php

namespace Mewtonium\Vanguard\Tests\Fixtures\Forms;

use Mewtonium\Vanguard\Vanguard;
use Mewtonium\Vanguard\Rules\GreaterThan;

class GreaterThanRuleForm {
    use Vanguard;

    public function __construct(
        #[GreaterThan(10)]
        protected int $num1,

        #[GreaterThan(10)]
        protected int $num2,

        #[GreaterThan(10, message: 'You must pick a number greater than 10')]
        protected int $num3,
    ) {
        //
    }
}
