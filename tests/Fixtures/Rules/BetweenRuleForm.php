<?php

namespace Mewtonium\Vanguard\Tests\Fixtures\Rules;

use Mewtonium\Vanguard\Vanguard;
use Mewtonium\Vanguard\Rules\Between;

class BetweenRuleForm {
    use Vanguard;

    public function __construct(
        #[Between(1, 10)]
        protected int $num1,

        #[Between(1, 10, message: 'You must pick a number between 1 and 10')]
        protected int $num2,
    ) {
        //
    }
}
