<?php

namespace Mewtonium\Vanguard\Tests\Fixtures\Rules;

use Mewtonium\Vanguard\Vanguard;
use Mewtonium\Vanguard\Rules\In;

class InRuleForm {
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
