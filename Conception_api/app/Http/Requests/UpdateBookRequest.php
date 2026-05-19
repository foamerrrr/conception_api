<?php

namespace App\Http\Requests;

use App\Models\Book;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $book = Book::where('isbn', $this->input('isbn'))->first();

        return [
            'isbn' => [
                'required',
                'string',
                'size:13',
                'exists:books,isbn',
                Rule::unique('books', 'isbn')->ignore($book),
            ],
            'title' => ['sometimes', 'required', 'string', 'min:3', 'max:255'],
            'author' => ['sometimes', 'required', 'string', 'min:3', 'max:100'],
            'summary' => ['sometimes', 'required', 'string', 'min:10', 'max:500'],
        ];
    }
}
