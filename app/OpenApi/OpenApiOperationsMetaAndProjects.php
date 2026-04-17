<?php

declare(strict_types=1);

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Get(
    path: '/v1',
    operationId: 'metaIndex',
    tags: ['Meta'],
    summary: 'Карта API и ссылки на разделы (без токена)',
    security: [],
    responses: [new OA\Response(response: 200, description: 'JSON со списком вариантов', content: new OA\JsonContent(ref: '#/components/schemas/JsonObject'))]
)]
#[OA\Get(
    path: '/v1/projects',
    operationId: 'projectsIndex',
    tags: ['Projects'],
    summary: 'Список проектов',
    parameters: [
        new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer')),
        new OA\Parameter(name: 'per_page', in: 'query', schema: new OA\Schema(type: 'integer')),
        new OA\Parameter(name: 'status', in: 'query', schema: new OA\Schema(type: 'string')),
        new OA\Parameter(name: 'q', in: 'query', description: 'Поиск по name, code, description', schema: new OA\Schema(type: 'string')),
        new OA\Parameter(name: 'sort', in: 'query', schema: new OA\Schema(type: 'string')),
        new OA\Parameter(name: 'direction', in: 'query', schema: new OA\Schema(type: 'string', enum: ['asc', 'desc'])),
    ],
    responses: [new OA\Response(response: 200, description: 'Пагинация', content: new OA\JsonContent(ref: '#/components/schemas/JsonObject'))]
)]
#[OA\Post(
    path: '/v1/projects',
    operationId: 'projectsStore',
    tags: ['Projects'],
    summary: 'Создать проект',
    requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ProjectWrite')),
    responses: [
        new OA\Response(response: 201, description: 'Создано', content: new OA\JsonContent(ref: '#/components/schemas/JsonObject')),
        new OA\Response(response: 422, description: 'Ошибка валидации', content: new OA\JsonContent(ref: '#/components/schemas/JsonObject')),
    ]
)]
#[OA\Get(
    path: '/v1/projects/{project}',
    operationId: 'projectsShow',
    tags: ['Projects'],
    summary: 'Проект с этапами и задачами',
    parameters: [new OA\Parameter(name: 'project', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
    responses: [
        new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/JsonObject')),
        new OA\Response(response: 404, description: 'Не найдено'),
    ]
)]
#[OA\Put(
    path: '/v1/projects/{project}',
    operationId: 'projectsUpdate',
    tags: ['Projects'],
    summary: 'Обновить проект (допускается также PATCH)',
    parameters: [new OA\Parameter(name: 'project', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
    requestBody: new OA\RequestBody(content: new OA\JsonContent(ref: '#/components/schemas/ProjectWrite')),
    responses: [
        new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/JsonObject')),
        new OA\Response(response: 422, description: 'Ошибка валидации', content: new OA\JsonContent(ref: '#/components/schemas/JsonObject')),
    ]
)]
#[OA\Delete(
    path: '/v1/projects/{project}',
    operationId: 'projectsDestroy',
    tags: ['Projects'],
    summary: 'Удалить проект',
    parameters: [new OA\Parameter(name: 'project', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
    responses: [new OA\Response(response: 204, description: 'Без тела')]
)]
#[OA\Get(
    path: '/v1/projects/{project}/milestones',
    operationId: 'milestonesIndex',
    tags: ['Projects'],
    summary: 'Этапы проекта',
    parameters: [
        new OA\Parameter(name: 'project', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer')),
        new OA\Parameter(name: 'per_page', in: 'query', schema: new OA\Schema(type: 'integer')),
        new OA\Parameter(name: 'q', in: 'query', description: 'Поиск по названию этапа', schema: new OA\Schema(type: 'string')),
        new OA\Parameter(name: 'sort', in: 'query', schema: new OA\Schema(type: 'string')),
        new OA\Parameter(name: 'direction', in: 'query', schema: new OA\Schema(type: 'string', enum: ['asc', 'desc'])),
    ],
    responses: [new OA\Response(response: 200, description: 'Пагинация', content: new OA\JsonContent(ref: '#/components/schemas/JsonObject'))]
)]
#[OA\Post(
    path: '/v1/projects/{project}/milestones',
    operationId: 'milestonesStore',
    tags: ['Projects'],
    summary: 'Создать этап',
    parameters: [new OA\Parameter(name: 'project', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
    requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/MilestoneWrite')),
    responses: [
        new OA\Response(response: 201, description: 'Создано', content: new OA\JsonContent(ref: '#/components/schemas/JsonObject')),
        new OA\Response(response: 422, description: 'Ошибка валидации', content: new OA\JsonContent(ref: '#/components/schemas/JsonObject')),
    ]
)]
#[OA\Get(
    path: '/v1/projects/{project}/milestones/{milestone}',
    operationId: 'milestonesShow',
    tags: ['Projects'],
    summary: 'Этап с задачами',
    parameters: [
        new OA\Parameter(name: 'project', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        new OA\Parameter(name: 'milestone', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
    responses: [new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/JsonObject'))]
)]
#[OA\Put(
    path: '/v1/projects/{project}/milestones/{milestone}',
    operationId: 'milestonesUpdate',
    tags: ['Projects'],
    summary: 'Обновить этап',
    parameters: [
        new OA\Parameter(name: 'project', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        new OA\Parameter(name: 'milestone', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
    requestBody: new OA\RequestBody(content: new OA\JsonContent(ref: '#/components/schemas/MilestoneWrite')),
    responses: [new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/JsonObject'))]
)]
#[OA\Delete(
    path: '/v1/projects/{project}/milestones/{milestone}',
    operationId: 'milestonesDestroy',
    tags: ['Projects'],
    summary: 'Удалить этап',
    parameters: [
        new OA\Parameter(name: 'project', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        new OA\Parameter(name: 'milestone', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
    responses: [new OA\Response(response: 204, description: 'Без тела')]
)]
#[OA\Get(
    path: '/v1/projects/{project}/tasks',
    operationId: 'tasksIndex',
    tags: ['Projects'],
    summary: 'Задачи проекта',
    parameters: [
        new OA\Parameter(name: 'project', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        new OA\Parameter(name: 'milestone_id', in: 'query', schema: new OA\Schema(type: 'integer')),
        new OA\Parameter(name: 'status', in: 'query', schema: new OA\Schema(type: 'string')),
        new OA\Parameter(name: 'priority', in: 'query', schema: new OA\Schema(type: 'string')),
        new OA\Parameter(name: 'employee_id', in: 'query', schema: new OA\Schema(type: 'integer')),
        new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer')),
        new OA\Parameter(name: 'per_page', in: 'query', schema: new OA\Schema(type: 'integer')),
        new OA\Parameter(name: 'q', in: 'query', description: 'Поиск по title, description', schema: new OA\Schema(type: 'string')),
        new OA\Parameter(name: 'sort', in: 'query', schema: new OA\Schema(type: 'string')),
        new OA\Parameter(name: 'direction', in: 'query', schema: new OA\Schema(type: 'string', enum: ['asc', 'desc'])),
    ],
    responses: [new OA\Response(response: 200, description: 'Пагинация', content: new OA\JsonContent(ref: '#/components/schemas/JsonObject'))]
)]
#[OA\Post(
    path: '/v1/projects/{project}/tasks',
    operationId: 'tasksStore',
    tags: ['Projects'],
    summary: 'Создать задачу',
    parameters: [new OA\Parameter(name: 'project', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
    requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/TaskWrite')),
    responses: [
        new OA\Response(response: 201, description: 'Создано', content: new OA\JsonContent(ref: '#/components/schemas/JsonObject')),
        new OA\Response(response: 422, description: 'Ошибка валидации', content: new OA\JsonContent(ref: '#/components/schemas/JsonObject')),
    ]
)]
#[OA\Get(
    path: '/v1/projects/{project}/tasks/{task}',
    operationId: 'tasksShow',
    tags: ['Projects'],
    summary: 'Задача',
    parameters: [
        new OA\Parameter(name: 'project', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        new OA\Parameter(name: 'task', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
    responses: [new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/JsonObject'))]
)]
#[OA\Put(
    path: '/v1/projects/{project}/tasks/{task}',
    operationId: 'tasksUpdate',
    tags: ['Projects'],
    summary: 'Обновить задачу',
    parameters: [
        new OA\Parameter(name: 'project', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        new OA\Parameter(name: 'task', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
    requestBody: new OA\RequestBody(content: new OA\JsonContent(ref: '#/components/schemas/TaskWrite')),
    responses: [new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/JsonObject'))]
)]
#[OA\Delete(
    path: '/v1/projects/{project}/tasks/{task}',
    operationId: 'tasksDestroy',
    tags: ['Projects'],
    summary: 'Удалить задачу',
    parameters: [
        new OA\Parameter(name: 'project', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        new OA\Parameter(name: 'task', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
    responses: [new OA\Response(response: 204, description: 'Без тела')]
)]
final class OpenApiOperationsMetaAndProjects {}
