<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name'])]
class CooperationType extends Model
{
    use HasFactory;

    public function proposals(): HasMany
    {
        return $this->hasMany(CollaborationProposal::class);
    }
}

