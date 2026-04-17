<?php

declare(strict_types=1);

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Post(
    path: '/v1/auth/login',
    operationId: 'authLogin',
    tags: ['Auth'],
    summary: 'Вход: выдаёт Bearer-токен Sanctum',
    security: [],
    requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/AuthLoginRequest')),
    responses: [
        new OA\Response(response: 200, description: 'Токен и профиль', content: new OA\JsonContent(ref: '#/components/schemas/AuthLoginResponse')),
        new OA\Response(response: 422, description: 'Неверные данные', content: new OA\JsonContent(ref: '#/components/schemas/JsonObject')),
    ]
)]
#[OA\Post(
    path: '/v1/auth/logout',
    operationId: 'authLogout',
    tags: ['Auth'],
    summary: 'Выход (отзыв текущего токена)',
    responses: [
        new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/JsonObject')),
    ]
)]
#[OA\Get(
    path: '/v1/auth/me',
    operationId: 'authMe',
    tags: ['Auth'],
    summary: 'Текущий пользователь и роль',
    responses: [
        new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/JsonObject')),
    ]
)]
final class OpenApiOperationsAuth {}
