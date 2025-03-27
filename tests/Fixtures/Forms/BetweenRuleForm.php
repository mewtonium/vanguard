<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Tests\Fixtures\Forms;

use Mewtonium\Vanguard\Rules\Between;
use Mewtonium\Vanguard\Vanguard;

final class BetweenRuleForm
{
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
