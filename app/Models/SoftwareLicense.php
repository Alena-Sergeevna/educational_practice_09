<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SoftwareLicense extends Model
{
    protected $fillable = ['name', 'total_seats', 'expires_at'];

    protected function casts(): array
    {
        return [
            'expires_at' => 'date',
        ];
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'software_license_employee')->withTimestamps();
    }

    public function usedSeatsCount(): int
    {
        return $this->employees()->count();
    }
}
