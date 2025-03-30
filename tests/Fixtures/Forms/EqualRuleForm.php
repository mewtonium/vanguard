<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Tests\Fixtures\Forms;

use Mewtonium\Vanguard\Rules\Equal;
use Mewtonium\Vanguard\Vanguard;

final class EqualRuleForm
{
    use Vanguard;

    public function __construct(
        #[Equal(10)]
        protected int $num1,
        #[Equal(3.495)]
        protected float $num2,
        #[Equal('test')]
        protected string $str1,
        #[Equal('testing', message: 'The value must equal "testing"')]
        protected string $str2,
        #[Equal('2025-01-01')]
        protected string $date,
    ) {
        //
    }
}
