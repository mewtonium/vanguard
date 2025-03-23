<?php

namespace Mewtonium\Vanguard\Tests\Fixtures\Rules;

use Mewtonium\Vanguard\Vanguard;
use Mewtonium\Vanguard\Rules\Email;

class EmailRuleForm {
    use Vanguard;

    public function __construct(
        #[Email]
        protected string $str1,

        #[Email(message: 'Your email is invalid')]
        protected string $str2,    
    ) {
        //
    }
}
