<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'password', 'phone', 'avatar'])]
#[Hidden(['password', 'remember_token'])]
class User extends Model
{
    use HasApiTokens;

    public function addresses(): HasMany {
        return $this->hasMany(Address::class);
    }

    public function cart(): HasOne {
        return $this->hasOne(Cart::class);
    }
}
