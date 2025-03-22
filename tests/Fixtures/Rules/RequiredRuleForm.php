<?php

namespace Mewtonium\Vanguard\Tests\Fixtures\Rules;

use Mewtonium\Vanguard\Vanguard;
use Mewtonium\Vanguard\Rules\Required;

class RequiredRuleForm {
    use Vanguard;

    public function __construct(
        #[Required]
        protected string $str1,
        
        #[Required(message: 'Please provide str2.')]
        protected string $str2,
    ) {
        //
    }
}
