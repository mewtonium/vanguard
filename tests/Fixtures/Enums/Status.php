<?php

declare(strict_types=1);

namespace Mewtonium\Vanguard\Tests\Fixtures\Enums;

enum Status: string
{
    case Active = 'active';
    case Inactive = 'inactive';
}
