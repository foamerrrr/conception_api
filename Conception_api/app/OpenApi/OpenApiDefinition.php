<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Conception API',
    description: 'API REST Laravel — authentification Sanctum et gestion des livres (pagination, cache, HATEOAS).',
)]
#[OA\Server(
    url: 'http://127.0.0.1:8000/api/v1',
    description: 'Serveur local (php artisan serve)',
)]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Sanctum',
    description: 'Token obtenu via POST /register ou POST /login. Format : Bearer {token}',
)]
#[OA\Schema(
    schema: 'User',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Test User'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'test@example.com'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ],
)]
#[OA\Schema(
    schema: 'AuthResponse',
    type: 'object',
    properties: [
        new OA\Property(property: 'user', ref: '#/components/schemas/User'),
        new OA\Property(property: 'token', type: 'string', example: '1|abcdefghijklmnopqrstuvwxyz'),
    ],
)]
#[OA\Schema(
    schema: 'BookLinks',
    type: 'object',
    properties: [
        new OA\Property(property: 'self', type: 'string', example: 'http://127.0.0.1:8000/api/v1/books/1'),
        new OA\Property(property: 'update', type: 'string', example: 'http://127.0.0.1:8000/api/v1/books/1'),
        new OA\Property(property: 'delete', type: 'string', example: 'http://127.0.0.1:8000/api/v1/books/1'),
        new OA\Property(property: 'all', type: 'string', example: 'http://127.0.0.1:8000/api/v1/books'),
    ],
)]
#[OA\Schema(
    schema: 'Book',
    type: 'object',
    properties: [
        new OA\Property(property: 'title', type: 'string', example: '1984'),
        new OA\Property(property: 'author', type: 'string', example: 'GEORGE ORWELL'),
        new OA\Property(property: 'summary', type: 'string', example: 'Roman dystopique décrivant une société totalitaire contrôlée par Big Brother.'),
        new OA\Property(property: 'isbn', type: 'string', example: '9780451524935'),
        new OA\Property(property: '_links', ref: '#/components/schemas/BookLinks'),
    ],
)]
#[OA\Schema(
    schema: 'BookInput',
    type: 'object',
    required: ['title', 'author', 'summary', 'isbn'],
    properties: [
        new OA\Property(property: 'title', type: 'string', minLength: 3, maxLength: 255, example: '1984'),
        new OA\Property(property: 'author', type: 'string', minLength: 3, maxLength: 100, example: 'George Orwell'),
        new OA\Property(property: 'summary', type: 'string', minLength: 10, maxLength: 500, example: 'Roman dystopique décrivant une société totalitaire contrôlée par Big Brother.'),
        new OA\Property(property: 'isbn', type: 'string', minLength: 13, maxLength: 13, example: '9780451524935'),
    ],
)]
#[OA\Schema(
    schema: 'BookUpdateInput',
    type: 'object',
    required: ['isbn'],
    properties: [
        new OA\Property(property: 'isbn', type: 'string', minLength: 13, maxLength: 13, example: '9780451524935'),
        new OA\Property(property: 'title', type: 'string', minLength: 3, maxLength: 255, example: '1984'),
        new OA\Property(property: 'author', type: 'string', minLength: 3, maxLength: 100, example: 'George Orwell'),
        new OA\Property(property: 'summary', type: 'string', minLength: 10, maxLength: 500, example: 'Roman dystopique décrivant une société totalitaire contrôlée par Big Brother.'),
    ],
)]
#[OA\Schema(
    schema: 'ValidationError',
    type: 'object',
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'The title field is required.'),
        new OA\Property(
            property: 'errors',
            type: 'object',
            example: ['title' => ['The title field is required.']],
        ),
    ],
)]
#[OA\Schema(
    schema: 'MessageResponse',
    type: 'object',
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'Logged out successfully'),
    ],
)]
class OpenApiDefinition
{
}
