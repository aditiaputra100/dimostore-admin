<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'password', 'phone', 'avatar'])]
#[Hidden(['password', 'remember_token'])]
class User extends Model
{
    use HasApiTokens;
}
