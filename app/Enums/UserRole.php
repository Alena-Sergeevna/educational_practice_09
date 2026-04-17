<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Projects = 'projects';
    case Org = 'org';
    case Assets = 'assets';
    case Tickets = 'tickets';
    case Hiring = 'hiring';
}
