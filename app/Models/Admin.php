<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'password'])]
#[Hidden(['password'])]
class Admin extends Model
{
    public function orderStatusHistories(): HasMany {
        return $this->hasMany(OrderStatusHistory::class, 'created_by');
    }
}
