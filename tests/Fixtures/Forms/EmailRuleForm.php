<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Tests\Fixtures\Forms;

use Mewtonium\Vanguard\Rules\Email;
use Mewtonium\Vanguard\Vanguard;

final class EmailRuleForm
{
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
