<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Tests\Fixtures\Forms;

use Mewtonium\Vanguard\Rules\MinLength;
use Mewtonium\Vanguard\Vanguard;

final class MinLengthRuleForm
{
    use Vanguard;

    public function __construct(
        #[MinLength(5)]
        protected string $val1,
        #[MinLength(5)]
        protected array $val2,
        #[MinLength(5)]
        protected string $val3,
        #[MinLength(5)]
        protected array $val4,
        #[MinLength(5, message: 'The length is too short')]
        protected string $val5,
        #[MinLength(5)]
        protected mixed $val6,
    ) {
        //
    }
}
