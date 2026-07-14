<?php

namespace App;

use Filament\Support\Contracts\HasLabel;

enum OrderStatus: string implements HasLabel
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Canceled = 'canceled';

    public function getIndex(): int {
        return array_search($this, self::cases(), true);
    }

    public function next(): ?self {
        return match ($this) {
            self::Pending => self::Processing,
            self::Processing => self::Shipped,
            self::Shipped => self::Delivered,
            default => null,
        };
    }

    public function canMoveForward(): bool {
        return $this->next() !== null;
    }

    public function canCancel(): bool {
        return match ($this) {
            self::Pending,
            self::Processing,
            self::Shipped => true,
            default => false,
        };
    }

    public function getLabel(): string|\Illuminate\Contracts\Support\Htmlable|null {
        return $this->name;
    }
}
