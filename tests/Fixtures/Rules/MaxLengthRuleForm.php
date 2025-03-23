<?php

namespace Mewtonium\Vanguard\Tests\Fixtures\Rules;

use Mewtonium\Vanguard\Rules\MaxLength;
use Mewtonium\Vanguard\Vanguard;

class MaxLengthRuleForm
{
    use Vanguard;

    public function __construct(
        #[MaxLength(5)]
        protected string $val1,

        #[MaxLength(5)]
        protected array $val2,

        #[MaxLength(5)]
        protected string $val3,

        #[MaxLength(5)]
        protected array $val4,

        #[MaxLength(5, message: 'The length is too long')]
        protected string $val5,
    ) {
        //
    }
}
