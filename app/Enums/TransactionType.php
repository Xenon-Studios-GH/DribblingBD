<?php

namespace App\Enums;

enum TransactionType: string
{
    case In = 'in';
    case Out = 'out';
}
