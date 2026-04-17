<?php

declare(strict_types=1);

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Get(path: '/v1/projects/analytics/overview', operationId: 'projectsAnalyticsOverview', tags: ['Projects'], summary: 'Аналитика: сводка по проектам и задачам', responses: [new OA\Response(response: 200, description: 'Агрегаты', content: new OA\JsonContent(ref: '#/components/schemas/JsonObject'))])]
#[OA\Get(path: '/v1/projects/analytics/tasks-by-priority', operationId: 'projectsAnalyticsTasksByPriority', tags: ['Projects'], summary: 'Аналитика: задачи по приоритету', responses: [new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/JsonObject'))])]
#[OA\Get(path: '/v1/projects/analytics/task-workload', operationId: 'projectsAnalyticsTaskWorkload', tags: ['Projects'], summary: 'Аналитика: нагрузка по исполнителям', responses: [new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/JsonObject'))])]
#[OA\Get(path: '/v1/org/analytics/employees-by-department', operationId: 'orgAnalyticsEmployeesByDepartment', tags: ['Org'], summary: 'Аналитика: численность по отделам', responses: [new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/JsonObject'))])]
#[OA\Get(path: '/v1/org/analytics/team-sizes', operationId: 'orgAnalyticsTeamSizes', tags: ['Org'], summary: 'Аналитика: размеры команд', responses: [new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/JsonObject'))])]
#[OA\Get(path: '/v1/org/analytics/job-title-distribution', operationId: 'orgAnalyticsJobTitles', tags: ['Org'], summary: 'Аналитика: распределение по должностям', responses: [new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/JsonObject'))])]
#[OA\Get(path: '/v1/assets/analytics/hardware-by-status', operationId: 'assetsAnalyticsHardwareByStatus', tags: ['Assets'], summary: 'Аналитика: оборудование по статусам', responses: [new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/JsonObject'))])]
#[OA\Get(path: '/v1/assets/analytics/license-utilization', operationId: 'assetsAnalyticsLicenseUtilization', tags: ['Assets'], summary: 'Аналитика: утилизация лицензий', responses: [new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/JsonObject'))])]
#[OA\Get(path: '/v1/assets/analytics/hardware-by-type', operationId: 'assetsAnalyticsHardwareByType', tags: ['Assets'], summary: 'Аналитика: оборудование по типам', responses: [new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/JsonObject'))])]
#[OA\Get(path: '/v1/tickets/analytics/records-by-status', operationId: 'ticketsAnalyticsByStatus', tags: ['Tickets'], summary: 'Аналитика: заявки по статусам', responses: [new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/JsonObject'))])]
#[OA\Get(path: '/v1/tickets/analytics/records-by-category', operationId: 'ticketsAnalyticsByCategory', tags: ['Tickets'], summary: 'Аналитика: заявки по категориям', responses: [new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/JsonObject'))])]
#[OA\Get(path: '/v1/tickets/analytics/records-by-priority', operationId: 'ticketsAnalyticsByPriority', tags: ['Tickets'], summary: 'Аналитика: заявки по приоритету', responses: [new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/JsonObject'))])]
#[OA\Get(path: '/v1/hiring/analytics/applications-by-stage', operationId: 'hiringAnalyticsApplicationsByStage', tags: ['Hiring'], summary: 'Аналитика: отклики по этапам', responses: [new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/JsonObject'))])]
#[OA\Get(path: '/v1/hiring/analytics/vacancy-applications', operationId: 'hiringAnalyticsVacancyApplications', tags: ['Hiring'], summary: 'Аналитика: отклики по вакансиям', responses: [new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/JsonObject'))])]
#[OA\Get(path: '/v1/hiring/analytics/upcoming-interviews', operationId: 'hiringAnalyticsUpcomingInterviews', tags: ['Hiring'], summary: 'Аналитика: предстоящие интервью', parameters: [new OA\Parameter(name: 'days', in: 'query', description: 'Горизонт в днях (1–365), по умолчанию 30', schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/JsonObject'))])]
final class OpenApiOperationsAnalytics {}
