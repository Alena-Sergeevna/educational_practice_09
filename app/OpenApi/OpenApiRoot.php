<?php

declare(strict_types=1);

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\OpenApi(
    openapi: '3.0.0',
    info: new OA\Info(
        title: 'IT Company Practice API',
        version: '1.0.0',
        description: 'Учебный REST API (Laravel) + Sanctum Bearer. Сначала POST /v1/auth/login, затем заголовок Authorization: Bearer {token}. Роли: admin (всё), projects, org, assets, tickets, hiring — только свой раздел. Списки: фильтры по полям раздела, `q` — поиск по текстовым полям, `sort` + `direction` (asc|desc), `page` и `per_page`. В каждом разделе есть GET `.../analytics/...` — сводки и отчёты. Публично: GET /v1 и POST /v1/auth/login. Swagger UI: /api/documentation.'
    ),
    servers: [
        new OA\Server(
            url: '/api',
            description: 'Префикс группы `api` в Laravel. При деплое задайте `APP_URL`; при «Try it out» в Swagger UI запросы идут на тот же хост.'
        ),
    ],
    security: [['bearerAuth' => []]],
    tags: [
        new OA\Tag(name: 'Auth', description: 'Sanctum: вход, выход, профиль'),
        new OA\Tag(name: 'Meta', description: 'Сводка и ссылки на разделы'),
        new OA\Tag(name: 'Projects', description: 'Вариант 1: проекты, этапы (milestones), задачи (tasks)'),
        new OA\Tag(name: 'Org', description: 'Вариант 2: отделы, сотрудники, команды'),
        new OA\Tag(name: 'Assets', description: 'Вариант 3: железо (hardware-assets), лицензии ПО'),
        new OA\Tag(name: 'Tickets', description: 'Вариант 4: категории заявок, заявки (`records`), комментарии'),
        new OA\Tag(name: 'Hiring', description: 'Вариант 5: вакансии, кандидаты, отклики (job-applications), интервью'),
    ],
    components: new OA\Components(
        schemas: [
            new OA\Schema(
                schema: 'JsonObject',
                type: 'object',
                additionalProperties: true,
                description: 'Произвольный JSON (модель Eloquent, пагинация Laravel, ошибки валидации).'
            ),
            new OA\Schema(
                schema: 'AuthLoginRequest',
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'projects@company.test'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password'),
                ]
            ),
            new OA\Schema(
                schema: 'AuthLoginResponse',
                required: ['token', 'token_type', 'user'],
                properties: [
                    new OA\Property(property: 'token', type: 'string'),
                    new OA\Property(property: 'token_type', type: 'string', example: 'Bearer'),
                    new OA\Property(
                        property: 'user',
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'id', type: 'integer'),
                            new OA\Property(property: 'name', type: 'string'),
                            new OA\Property(property: 'email', type: 'string'),
                            new OA\Property(property: 'role', type: 'string', enum: ['admin', 'projects', 'org', 'assets', 'tickets', 'hiring']),
                        ]
                    ),
                ]
            ),
            new OA\Schema(
                schema: 'ProjectWrite',
                required: ['name'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Портал сотрудника'),
                    new OA\Property(property: 'code', type: 'string', nullable: true),
                    new OA\Property(property: 'status', type: 'string', nullable: true, example: 'active'),
                    new OA\Property(property: 'description', type: 'string', nullable: true),
                    new OA\Property(property: 'started_at', type: 'string', format: 'date', nullable: true),
                    new OA\Property(property: 'ended_at', type: 'string', format: 'date', nullable: true),
                ]
            ),
            new OA\Schema(
                schema: 'MilestoneWrite',
                required: ['name'],
                properties: [
                    new OA\Property(property: 'name', type: 'string'),
                    new OA\Property(property: 'due_date', type: 'string', format: 'date', nullable: true),
                    new OA\Property(property: 'sort_order', type: 'integer', nullable: true),
                ]
            ),
            new OA\Schema(
                schema: 'TaskWrite',
                required: ['title'],
                properties: [
                    new OA\Property(property: 'milestone_id', type: 'integer', nullable: true),
                    new OA\Property(property: 'employee_id', type: 'integer', nullable: true),
                    new OA\Property(property: 'title', type: 'string'),
                    new OA\Property(property: 'description', type: 'string', nullable: true),
                    new OA\Property(property: 'status', type: 'string', nullable: true),
                    new OA\Property(property: 'priority', type: 'string', nullable: true),
                    new OA\Property(property: 'due_date', type: 'string', format: 'date', nullable: true),
                ]
            ),
            new OA\Schema(
                schema: 'DepartmentWrite',
                required: ['name'],
                properties: [
                    new OA\Property(property: 'name', type: 'string'),
                    new OA\Property(property: 'code', type: 'string', nullable: true),
                ]
            ),
            new OA\Schema(
                schema: 'EmployeeWrite',
                required: ['first_name', 'last_name', 'email'],
                properties: [
                    new OA\Property(property: 'department_id', type: 'integer', nullable: true),
                    new OA\Property(property: 'first_name', type: 'string'),
                    new OA\Property(property: 'last_name', type: 'string'),
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                    new OA\Property(property: 'job_title', type: 'string', nullable: true),
                    new OA\Property(property: 'hired_at', type: 'string', format: 'date', nullable: true),
                ]
            ),
            new OA\Schema(
                schema: 'TeamWrite',
                required: ['name'],
                properties: [
                    new OA\Property(property: 'name', type: 'string'),
                    new OA\Property(property: 'description', type: 'string', nullable: true),
                ]
            ),
            new OA\Schema(
                schema: 'TeamAttachRequest',
                required: ['employee_id'],
                properties: [
                    new OA\Property(property: 'employee_id', type: 'integer'),
                ]
            ),
            new OA\Schema(
                schema: 'HardwareAssetWrite',
                required: ['name', 'inventory_number'],
                properties: [
                    new OA\Property(property: 'name', type: 'string'),
                    new OA\Property(property: 'type', type: 'string', nullable: true),
                    new OA\Property(property: 'inventory_number', type: 'string'),
                    new OA\Property(property: 'status', type: 'string', nullable: true, enum: ['in_stock', 'assigned', 'retired']),
                    new OA\Property(property: 'employee_id', type: 'integer', nullable: true),
                    new OA\Property(property: 'notes', type: 'string', nullable: true),
                ]
            ),
            new OA\Schema(
                schema: 'SoftwareLicenseWrite',
                required: ['name', 'total_seats'],
                properties: [
                    new OA\Property(property: 'name', type: 'string'),
                    new OA\Property(property: 'total_seats', type: 'integer'),
                    new OA\Property(property: 'expires_at', type: 'string', format: 'date', nullable: true),
                ]
            ),
            new OA\Schema(
                schema: 'LicenseAttachRequest',
                required: ['employee_id'],
                properties: [
                    new OA\Property(property: 'employee_id', type: 'integer'),
                ]
            ),
            new OA\Schema(
                schema: 'TicketCategoryWrite',
                required: ['name'],
                properties: [
                    new OA\Property(property: 'name', type: 'string'),
                    new OA\Property(property: 'slug', type: 'string', nullable: true),
                ]
            ),
            new OA\Schema(
                schema: 'TicketRecordWrite',
                required: ['ticket_category_id', 'reporter_id', 'subject'],
                properties: [
                    new OA\Property(property: 'ticket_category_id', type: 'integer'),
                    new OA\Property(property: 'reporter_id', type: 'integer'),
                    new OA\Property(property: 'assignee_id', type: 'integer', nullable: true),
                    new OA\Property(property: 'subject', type: 'string'),
                    new OA\Property(property: 'body', type: 'string', nullable: true),
                    new OA\Property(property: 'priority', type: 'string', nullable: true),
                    new OA\Property(property: 'status', type: 'string', nullable: true),
                ]
            ),
            new OA\Schema(
                schema: 'TicketCommentWrite',
                required: ['employee_id', 'body'],
                properties: [
                    new OA\Property(property: 'employee_id', type: 'integer'),
                    new OA\Property(property: 'body', type: 'string'),
                ]
            ),
            new OA\Schema(
                schema: 'TicketCommentPatch',
                properties: [
                    new OA\Property(property: 'body', type: 'string'),
                ]
            ),
            new OA\Schema(
                schema: 'VacancyWrite',
                required: ['title'],
                properties: [
                    new OA\Property(property: 'title', type: 'string'),
                    new OA\Property(property: 'department_id', type: 'integer', nullable: true),
                    new OA\Property(property: 'description', type: 'string', nullable: true),
                    new OA\Property(property: 'status', type: 'string', nullable: true),
                    new OA\Property(property: 'opened_at', type: 'string', format: 'date', nullable: true),
                ]
            ),
            new OA\Schema(
                schema: 'CandidateWrite',
                required: ['full_name', 'email'],
                properties: [
                    new OA\Property(property: 'full_name', type: 'string'),
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                    new OA\Property(property: 'phone', type: 'string', nullable: true),
                ]
            ),
            new OA\Schema(
                schema: 'JobApplicationWrite',
                required: ['vacancy_id', 'candidate_id'],
                properties: [
                    new OA\Property(property: 'vacancy_id', type: 'integer'),
                    new OA\Property(property: 'candidate_id', type: 'integer'),
                    new OA\Property(property: 'stage', type: 'string', nullable: true),
                    new OA\Property(property: 'applied_at', type: 'string', format: 'date-time', nullable: true),
                ]
            ),
            new OA\Schema(
                schema: 'JobApplicationPatch',
                properties: [
                    new OA\Property(property: 'stage', type: 'string'),
                    new OA\Property(property: 'applied_at', type: 'string', format: 'date', nullable: true),
                ]
            ),
            new OA\Schema(
                schema: 'InterviewWrite',
                required: ['scheduled_at'],
                properties: [
                    new OA\Property(property: 'scheduled_at', type: 'string', format: 'date-time'),
                    new OA\Property(property: 'notes', type: 'string', nullable: true),
                    new OA\Property(property: 'interviewer_id', type: 'integer', nullable: true),
                ]
            ),
            new OA\Schema(
                schema: 'InterviewPatch',
                properties: [
                    new OA\Property(property: 'scheduled_at', type: 'string', format: 'date-time'),
                    new OA\Property(property: 'notes', type: 'string', nullable: true),
                    new OA\Property(property: 'interviewer_id', type: 'integer', nullable: true),
                ]
            ),
        ],
        securitySchemes: [
            new OA\SecurityScheme(
                securityScheme: 'bearerAuth',
                type: 'http',
                scheme: 'bearer',
                bearerFormat: 'Sanctum',
                description: 'Токен из POST /v1/auth/login (поле token). Заголовок: Authorization: Bearer <token>'
            ),
        ]
    )
)]
final class OpenApiRoot {}
