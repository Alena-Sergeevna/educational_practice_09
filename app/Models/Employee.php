<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $fillable = [
        'department_id',
        'first_name',
        'last_name',
        'email',
        'job_title',
        'hired_at',
    ];

    protected function casts(): array
    {
        return [
            'hired_at' => 'date',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_employee')->withTimestamps();
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function hardwareAssets(): HasMany
    {
        return $this->hasMany(HardwareAsset::class);
    }

    public function softwareLicenses(): BelongsToMany
    {
        return $this->belongsToMany(SoftwareLicense::class, 'software_license_employee')->withTimestamps();
    }

    public function reportedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'reporter_id');
    }

    public function assignedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'assignee_id');
    }

    public function ticketComments(): HasMany
    {
        return $this->hasMany(TicketComment::class);
    }

    public function interviewsAsInterviewer(): HasMany
    {
        return $this->hasMany(Interview::class, 'interviewer_id');
    }
}
