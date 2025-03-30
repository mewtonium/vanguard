<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Tests\Fixtures\Forms;

use Mewtonium\Vanguard\Rules\LessThan;
use Mewtonium\Vanguard\Vanguard;

final class LessThanRuleForm
{
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
