<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class BookController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $validated = $request->validate([
            'page' => ['sometimes', 'integer', 'min:1'],
        ]);

        $page = $validated['page'] ?? 1;

        return BookResource::collection(
            Book::latest()->paginate(perPage: 2, page: $page),
        );
    }

    public function store(StoreBookRequest $request): BookResource
    {
        $book = Book::create($request->validated());

        return new BookResource($book);
    }

    public function show(Request $request): BookResource
    {
        $validated = $request->validate([
            'isbn' => ['required', 'string', 'size:13', 'exists:books,isbn'],
        ]);

        $isbn = $validated['isbn'];

        $bookData = Cache::remember(
            "book.{$isbn}",
            now()->addMinutes(60),
            fn () => Book::where('isbn', $isbn)->firstOrFail()->attributesToArray(),
        );

        $book = (new Book)->newFromBuilder($bookData);

        return new BookResource($book);
    }

    public function update(UpdateBookRequest $request): BookResource
    {
        $book = Book::where('isbn', $request->input('isbn'))->firstOrFail();
        $previousIsbn = $book->isbn;
        $book->update($request->validated());

        Cache::forget("book.{$previousIsbn}");
        if ($book->isbn !== $previousIsbn) {
            Cache::forget("book.{$book->isbn}");
        }

        return new BookResource($book);
    }

    public function destroy(Request $request): Response
    {
        $validated = $request->validate([
            'isbn' => ['required', 'string', 'size:13', 'exists:books,isbn'],
        ]);

        $book = Book::where('isbn', $validated['isbn'])->firstOrFail();

        Cache::forget("book.{$book->isbn}");
        $book->delete();

        return response()->noContent();
    }
}
