<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class BookController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return BookResource::collection(Book::latest()->paginate(2));
    }

    public function store(StoreBookRequest $request): BookResource
    {
        $book = Book::create($request->validated());

        return new BookResource($book);
    }

    public function show(Book $book): BookResource
    {
        $cachedBook = Cache::remember(
            "book.{$book->isbn}",
            now()->addMinutes(60),
            fn () => $book,
        );

        return new BookResource($cachedBook);
    }

    public function update(UpdateBookRequest $request, Book $book): BookResource
    {
        $previousIsbn = $book->isbn;
        $book->update($request->validated());

        Cache::forget("book.{$previousIsbn}");
        if ($book->isbn !== $previousIsbn) {
            Cache::forget("book.{$book->isbn}");
        }

        return new BookResource($book);
    }

    public function destroy(Book $book): Response
    {
        Cache::forget("book.{$book->isbn}");
        $book->delete();

        return response()->noContent();
    }
}
