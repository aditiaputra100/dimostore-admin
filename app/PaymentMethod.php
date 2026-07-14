<?php

namespace App;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum PaymentMethod: string implements HasLabel
{
    case Bank = 'bank_transfer';
    case Qris = 'qris';
    case Cod = 'cod';   

    public function getLabel(): string | Htmlable | null {
        return strtoupper(implode(' ', explode('_', $this->value)));
    }

}
