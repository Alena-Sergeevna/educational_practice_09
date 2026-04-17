<?php

use App\Http\Controllers\Api\Assets\AssetsAnalyticsController;
use App\Http\Controllers\Api\Assets\HardwareAssetController;
use App\Http\Controllers\Api\Assets\SoftwareLicenseController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Hiring\CandidateController;
use App\Http\Controllers\Api\Hiring\HiringAnalyticsController;
use App\Http\Controllers\Api\Hiring\InterviewController;
use App\Http\Controllers\Api\Hiring\JobApplicationController;
use App\Http\Controllers\Api\Hiring\VacancyController;
use App\Http\Controllers\Api\Org\OrgAnalyticsController;
use App\Http\Controllers\Api\Org\DepartmentController;
use App\Http\Controllers\Api\Org\EmployeeController;
use App\Http\Controllers\Api\Org\TeamController;
use App\Http\Controllers\Api\Projects\MilestoneController;
use App\Http\Controllers\Api\Projects\ProjectAnalyticsController;
use App\Http\Controllers\Api\Projects\ProjectController;
use App\Http\Controllers\Api\Projects\TaskController;
use App\Http\Controllers\Api\Tickets\TicketsAnalyticsController;
use App\Http\Controllers\Api\Tickets\TicketCategoryController;
use App\Http\Controllers\Api\Tickets\TicketCommentController;
use App\Http\Controllers\Api\Tickets\TicketController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/', function () {
        return response()->json([
            'title' => 'IT Company Practice API',
            'auth' => [
                'login_url' => url('/api/v1/auth/login'),
                'method' => 'POST',
                'body_fields' => ['email', 'password'],
                'token_header' => 'Authorization: Bearer <token>',
                'demo_password' => 'password',
                'demo_accounts' => [
                    'admin@company.test' => 'admin — все разделы',
                    'projects@company.test' => 'projects',
                    'org@company.test' => 'org',
                    'assets@company.test' => 'assets',
                    'tickets@company.test' => 'tickets',
                    'hiring@company.test' => 'hiring',
                ],
            ],
            'variants' => [
                'projects' => url('/api/v1/projects'),
                'org' => url('/api/v1/org/departments'),
                'assets' => url('/api/v1/assets/hardware-assets'),
                'tickets' => url('/api/v1/tickets/ticket-categories'),
                'hiring' => url('/api/v1/hiring/vacancies'),
            ],
        ]);
    });

    Route::post('auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me', [AuthController::class, 'me']);

        Route::middleware('role:admin,projects')->group(function () {
            Route::get('projects/analytics/overview', [ProjectAnalyticsController::class, 'overview']);
            Route::get('projects/analytics/tasks-by-priority', [ProjectAnalyticsController::class, 'tasksByPriority']);
            Route::get('projects/analytics/task-workload', [ProjectAnalyticsController::class, 'taskWorkload']);
            Route::apiResource('projects', ProjectController::class);
            Route::apiResource('projects.milestones', MilestoneController::class);
            Route::apiResource('projects.tasks', TaskController::class);
        });

        Route::prefix('org')->middleware('role:admin,org')->group(function () {
            Route::get('analytics/employees-by-department', [OrgAnalyticsController::class, 'employeesByDepartment']);
            Route::get('analytics/team-sizes', [OrgAnalyticsController::class, 'teamSizes']);
            Route::get('analytics/job-title-distribution', [OrgAnalyticsController::class, 'jobTitleDistribution']);
            Route::apiResource('departments', DepartmentController::class);
            Route::apiResource('employees', EmployeeController::class);
            Route::apiResource('teams', TeamController::class);
            Route::post('teams/{team}/employees', [TeamController::class, 'attachEmployee']);
            Route::delete('teams/{team}/employees/{employee}', [TeamController::class, 'detachEmployee']);
        });

        Route::prefix('assets')->middleware('role:admin,assets')->group(function () {
            Route::get('analytics/hardware-by-status', [AssetsAnalyticsController::class, 'hardwareByStatus']);
            Route::get('analytics/license-utilization', [AssetsAnalyticsController::class, 'licenseUtilization']);
            Route::get('analytics/hardware-by-type', [AssetsAnalyticsController::class, 'hardwareByType']);
            Route::apiResource('hardware-assets', HardwareAssetController::class);
            Route::apiResource('software-licenses', SoftwareLicenseController::class);
            Route::post('software-licenses/{software_license}/employees', [SoftwareLicenseController::class, 'attachEmployee']);
            Route::delete('software-licenses/{software_license}/employees/{employee}', [SoftwareLicenseController::class, 'detachEmployee']);
        });

        Route::prefix('tickets')->middleware('role:admin,tickets')->group(function () {
            Route::get('analytics/records-by-status', [TicketsAnalyticsController::class, 'recordsByStatus']);
            Route::get('analytics/records-by-category', [TicketsAnalyticsController::class, 'recordsByCategory']);
            Route::get('analytics/records-by-priority', [TicketsAnalyticsController::class, 'recordsByPriority']);
            Route::apiResource('ticket-categories', TicketCategoryController::class);
            Route::apiResource('records', TicketController::class);
            Route::apiResource('records.ticket-comments', TicketCommentController::class);
        });

        Route::prefix('hiring')->middleware('role:admin,hiring')->group(function () {
            Route::get('analytics/applications-by-stage', [HiringAnalyticsController::class, 'applicationsByStage']);
            Route::get('analytics/vacancy-applications', [HiringAnalyticsController::class, 'vacancyApplications']);
            Route::get('analytics/upcoming-interviews', [HiringAnalyticsController::class, 'upcomingInterviews']);
            Route::apiResource('vacancies', VacancyController::class);
            Route::apiResource('candidates', CandidateController::class);
            Route::get('vacancies/{vacancy}/applications', [JobApplicationController::class, 'indexByVacancy']);
            Route::apiResource('job-applications', JobApplicationController::class);
            Route::apiResource('job-applications.interviews', InterviewController::class);
        });
    });
});
