<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'image', 'url', 'sort_order', 'is_active']), WithoutTimestamps]
class Banner extends Model
{
    //
}
