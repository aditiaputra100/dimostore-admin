<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['parent_id', 'name', 'description', 'image', 'is_active', 'deleted_at'])]
class Category extends Model
{
    //
}
