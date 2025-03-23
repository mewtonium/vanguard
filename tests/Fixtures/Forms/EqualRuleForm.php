<?php

namespace Mewtonium\Vanguard\Tests\Fixtures\Forms;

use Mewtonium\Vanguard\Vanguard;
use Mewtonium\Vanguard\Rules\Equal;

class EqualRuleForm {
    use Vanguard;

    public function __construct(
        #[Equal(10)]
        protected int $num1,

        #[Equal(3.495)]
        protected float $num2,

        #[Equal('test')]
        protected string $str1,

        #[Equal('testing', message: 'str2 must equal "testing"')]
        protected string $str2,
    ) {
        //
    }
}
