<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Tests\Fixtures\Forms;

use Mewtonium\Vanguard\Rules\GreaterThan;
use Mewtonium\Vanguard\Vanguard;

final class GreaterThanRuleForm
{
    use Vanguard;

    public function __construct(
        #[GreaterThan(10)]
        protected int $num1,
        #[GreaterThan(10)]
        protected int $num2,
        #[GreaterThan(10, message: 'You must pick a number greater than 10')]
        protected int $num3,
        #[GreaterThan('2025-01-01')]
        protected string $date,
    ) {
        //
    }
}
