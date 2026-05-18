<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BookController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return BookResource::collection(Book::latest()->get());
    }

    public function store(StoreBookRequest $request): JsonResponse
    {
        $book = Book::create($request->validated());

        return (new BookResource($book))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Book $book): BookResource
    {
        return new BookResource($book);
    }

    public function update(UpdateBookRequest $request, Book $book): BookResource
    {
        $book->update($request->validated());

        return new BookResource($book);
    }

    public function destroy(Book $book): Response
    {
        $book->delete();

        return response()->noContent();
    }
}
