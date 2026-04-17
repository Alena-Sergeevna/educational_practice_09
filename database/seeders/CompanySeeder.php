<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Department;
use App\Models\Employee;
use App\Models\HardwareAsset;
use App\Models\JobApplication;
use App\Models\Milestone;
use App\Models\Project;
use App\Models\SoftwareLicense;
use App\Models\Task;
use App\Models\Team;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketComment;
use App\Models\Vacancy;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $dev = Department::create(['name' => 'Разработка', 'code' => 'DEV']);
        $hr = Department::create(['name' => 'HR', 'code' => 'HR']);
        $it = Department::create(['name' => 'IT-поддержка', 'code' => 'IT']);

        $e1 = Employee::create([
            'department_id' => $dev->id,
            'first_name' => 'Анна',
            'last_name' => 'Смирнова',
            'email' => 'anna.smirnova@company.test',
            'job_title' => 'Senior Backend',
            'hired_at' => '2022-03-01',
        ]);
        $e2 = Employee::create([
            'department_id' => $dev->id,
            'first_name' => 'Иван',
            'last_name' => 'Козлов',
            'email' => 'ivan.kozlov@company.test',
            'job_title' => 'Frontend',
            'hired_at' => '2023-06-15',
        ]);
        $e3 = Employee::create([
            'department_id' => $hr->id,
            'first_name' => 'Мария',
            'last_name' => 'Петрова',
            'email' => 'maria.petrova@company.test',
            'job_title' => 'Рекрутер',
            'hired_at' => '2021-01-10',
        ]);

        $team = Team::create(['name' => 'Команда платформы', 'description' => 'Ядро продукта']);
        $team->employees()->attach([$e1->id, $e2->id]);

        $project = Project::create([
            'name' => 'Портал сотрудника',
            'code' => 'EMP-PORTAL',
            'status' => 'active',
            'description' => 'Внутренний портал',
            'started_at' => '2025-01-01',
        ]);
        $m1 = Milestone::create(['project_id' => $project->id, 'name' => 'MVP', 'due_date' => '2025-06-01', 'sort_order' => 1]);
        Task::create([
            'project_id' => $project->id,
            'milestone_id' => $m1->id,
            'employee_id' => $e1->id,
            'title' => 'API отделов',
            'status' => 'in_progress',
            'priority' => 'high',
            'due_date' => '2025-05-01',
        ]);
        Task::create([
            'project_id' => $project->id,
            'milestone_id' => $m1->id,
            'employee_id' => $e2->id,
            'title' => 'Макет списка задач',
            'status' => 'todo',
            'priority' => 'normal',
        ]);

        HardwareAsset::create([
            'name' => 'ThinkPad P14s',
            'type' => 'laptop',
            'inventory_number' => 'HW-0001',
            'status' => 'assigned',
            'employee_id' => $e1->id,
        ]);
        HardwareAsset::create([
            'name' => 'Dell UltraSharp 27',
            'type' => 'monitor',
            'inventory_number' => 'HW-0002',
            'status' => 'in_stock',
        ]);

        $lic = SoftwareLicense::create([
            'name' => 'JetBrains All Products',
            'total_seats' => 5,
            'expires_at' => '2026-12-31',
        ]);
        $lic->employees()->attach([$e1->id, $e2->id]);

        $catAccess = TicketCategory::create(['name' => 'Доступы', 'slug' => 'access']);
        $catSoft = TicketCategory::create(['name' => 'ПО', 'slug' => 'software']);

        $ticket = Ticket::create([
            'ticket_category_id' => $catAccess->id,
            'reporter_id' => $e2->id,
            'assignee_id' => $e3->id,
            'subject' => 'Нужен VPN',
            'body' => 'Не подключается к корпоративному VPN',
            'priority' => 'normal',
            'status' => 'open',
        ]);
        TicketComment::create([
            'ticket_id' => $ticket->id,
            'employee_id' => $e3->id,
            'body' => 'Проверьте профиль — отправил инструкцию на почту.',
        ]);

        $vac = Vacancy::create([
            'title' => 'Middle PHP Developer',
            'department_id' => $dev->id,
            'description' => 'Laravel, REST, SQL',
            'status' => 'open',
            'opened_at' => now()->toDateString(),
        ]);

        $cand = Candidate::create([
            'full_name' => 'Алексей Новиков',
            'email' => 'alexey.novikov@example.test',
            'phone' => '+79001234567',
        ]);

        $app = JobApplication::create([
            'vacancy_id' => $vac->id,
            'candidate_id' => $cand->id,
            'stage' => 'tech_interview',
            'applied_at' => now()->subDays(3),
        ]);

        $app->interviews()->create([
            'scheduled_at' => now()->addDays(2)->setTime(14, 0),
            'interviewer_id' => $e1->id,
            'notes' => 'Техсобес, стек PHP',
        ]);
    }
}
