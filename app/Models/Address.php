<?php

namespace App\Models;

use Database\Factories\AddressFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['recipient_name', 'phone', 'address_line', 'city', 'province', 'postal_code', 'is_default'])]
#[UseFactory(AddressFactory::class)]
class Address extends Model
{
    use HasFactory;
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function getAddress(): string {
        return $this->address_line . ', ' . $this->city . ', ' .
            $this->province . ', ' . $this->postal_code;
    }
}
