<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Tests\Fixtures\Forms;

use Mewtonium\Vanguard\Rules\In;
use Mewtonium\Vanguard\Vanguard;

final class InRuleForm
{
    use Vanguard;

    public function __construct(
        #[In(['GB', 'DE', 'FR'])]
        protected string $val1,
        #[In([100, 200, 300])]
        protected int $val2,
        #[In(['GB', 'DE', 'FR'], message: 'Please pick a value')]
        protected string $val3,
    ) {
        //
    }
}
