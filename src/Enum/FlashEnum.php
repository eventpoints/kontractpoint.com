<?php

declare(strict_types=1);

namespace App\Enum;

enum FlashEnum: string
{
    case SUCCESS = 'success';
    case ERROR = 'error';
    case MESSAGE = 'message';
}
