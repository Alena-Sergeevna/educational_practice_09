<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Team extends Model
{
    protected $fillable = ['name', 'description'];

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'team_employee')->withTimestamps();
    }
}
