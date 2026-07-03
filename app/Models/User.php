<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'password', 'phone', 'avatar'])]
#[Hidden(['password', 'remember_token'])]
#[UseFactory(UserFactory::class)]
class User extends Model
{
    use HasApiTokens, HasFactory;

    public function addresses(): HasMany {
        return $this->hasMany(Address::class);
    }

    public function cart(): HasOne {
        return $this->hasOne(Cart::class);
    }

    public function orders(): HasMany {
        return $this->hasMany(Order::class);
    }
}
