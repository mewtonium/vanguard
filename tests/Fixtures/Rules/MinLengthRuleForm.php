<?php

namespace Mewtonium\Vanguard\Tests\Fixtures\Rules;

use Mewtonium\Vanguard\Rules\MinLength;
use Mewtonium\Vanguard\Vanguard;

class MinLengthRuleForm
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
    ) {
        //
    }
}
