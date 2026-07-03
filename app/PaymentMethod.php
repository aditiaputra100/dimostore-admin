<?php

namespace App;

enum PaymentMethod: string
{
    case Bank = 'bank_transfer';
    case Qris = 'qris';
    case Cod = 'cod';
}
