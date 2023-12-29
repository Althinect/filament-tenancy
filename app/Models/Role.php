<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Role extends \Spatie\Permission\Models\Role
{
    use HasFactory;

    protected array $guard_name = ['api', 'web'];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
