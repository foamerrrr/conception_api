<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use OpenApi\Attributes as OA;

class BookController extends Controller
{
    #[OA\Get(
        path: '/books',
        operationId: 'booksIndex',
        tags: ['Books'],
        summary: 'Liste des livres',
        description: 'Retourne la liste paginée (2 livres par page), triée par date de création décroissante.',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Liste paginée',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/Book'),
                        ),
                        new OA\Property(property: 'links', type: 'object'),
                        new OA\Property(property: 'meta', type: 'object'),
                    ],
                ),
            ),
        ],
    )]
    public function index(): AnonymousResourceCollection
    {
        return BookResource::collection(
            Book::latest()->paginate(perPage: 2),
        );
    }

    #[OA\Post(
        path: '/books',
        operationId: 'booksStore',
        tags: ['Books'],
        summary: 'Créer un livre',
        description: 'Crée un nouveau livre. Route protégée par Sanctum.',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/BookInput'),
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Livre créé',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/Book'),
                    ],
                ),
            ),
            new OA\Response(response: 401, description: 'Non authentifié'),
            new OA\Response(
                response: 422,
                description: 'Erreur de validation',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationError'),
            ),
        ],
    )]
    public function store(StoreBookRequest $request): BookResource
    {
        $book = Book::create($request->validated());

        return new BookResource($book);
    }

    #[OA\Get(
        path: '/books/{book}',
        operationId: 'booksShow',
        tags: ['Books'],
        summary: 'Détail d\'un livre',
        description: 'Affiche un livre par son identifiant. Réponse mise en cache 60 minutes (clé book.{id}).',
        parameters: [
            new OA\Parameter(
                name: 'book',
                in: 'path',
                required: true,
                description: 'Identifiant du livre',
                schema: new OA\Schema(type: 'integer', example: 1),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Détail du livre (cache 60 min)',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/Book'),
                    ],
                ),
            ),
            new OA\Response(response: 404, description: 'Livre introuvable'),
        ],
    )]
    public function show(Book $book): JsonResponse
    {
        $bookData = Cache::remember(
            "book.{$book->id}",
            now()->addMinutes(60),
            fn () => (new BookResource($book))->resolve(),
        );

        return response()->json(['data' => $bookData]);
    }

    #[OA\Put(
        path: '/books/{book}',
        operationId: 'booksUpdate',
        tags: ['Books'],
        summary: 'Modifier un livre (PUT)',
        description: 'Met à jour un livre. Champs optionnels avec PATCH (sometimes). Invalide le cache.',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'book',
                in: 'path',
                required: true,
                description: 'Identifiant du livre',
                schema: new OA\Schema(type: 'integer', example: 1),
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/BookUpdateInput'),
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Livre mis à jour',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/Book'),
                    ],
                ),
            ),
            new OA\Response(response: 401, description: 'Non authentifié'),
            new OA\Response(response: 404, description: 'Livre introuvable'),
            new OA\Response(
                response: 422,
                description: 'Erreur de validation',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationError'),
            ),
        ],
    )]
    #[OA\Patch(
        path: '/books/{book}',
        operationId: 'booksPatch',
        tags: ['Books'],
        summary: 'Modifier un livre (PATCH)',
        description: 'Mise à jour partielle — seuls les champs envoyés sont modifiés (sometimes).',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'book',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1),
            ),
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(ref: '#/components/schemas/BookUpdateInput'),
        ),
        responses: [
            new OA\Response(response: 200, description: 'Livre mis à jour'),
            new OA\Response(response: 401, description: 'Non authentifié'),
            new OA\Response(response: 404, description: 'Livre introuvable'),
            new OA\Response(response: 422, description: 'Erreur de validation'),
        ],
    )]
    public function update(UpdateBookRequest $request, Book $book): BookResource
    {
        $book->update($request->validated());

        Cache::forget("book.{$book->id}");

        return new BookResource($book);
    }

    #[OA\Delete(
        path: '/books/{book}',
        operationId: 'booksDestroy',
        tags: ['Books'],
        summary: 'Supprimer un livre',
        description: 'Supprime un livre et invalide son cache. Route protégée.',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'book',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1),
            ),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Suppression réussie (corps vide)'),
            new OA\Response(response: 401, description: 'Non authentifié'),
            new OA\Response(response: 404, description: 'Livre introuvable'),
        ],
    )]
    public function destroy(Book $book): Response
    {
        Cache::forget("book.{$book->id}");
        $book->delete();

        return response()->noContent();
    }
}
