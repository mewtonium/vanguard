<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Tests\Fixtures\Forms;

use Mewtonium\Vanguard\Rules\Required;
use Mewtonium\Vanguard\Vanguard;

final class RequiredRuleForm
{
    use Vanguard;

    public function __construct(
        #[Required]
        protected string $str1,
        #[Required(message: 'Please provide str2.')]
        protected string $str2,
        #[Required]
        protected array $data,
        #[Required]
        protected mixed $val,
    ) {
        //
    }
}
