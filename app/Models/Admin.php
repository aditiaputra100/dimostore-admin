<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authentitacable;
use Override;

#[Fillable(['name', 'password'])]
#[Hidden(['password'])]
class Admin extends Authentitacable implements FilamentUser
{
    public function orderStatusHistories(): HasMany {
        return $this->hasMany(OrderStatusHistory::class, 'created_by');
    }

    #[Override]
    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
}
