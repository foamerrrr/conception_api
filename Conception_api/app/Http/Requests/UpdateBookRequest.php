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
        /** @var Book $book */
        $book = $this->route('book');

        return [
            'title' => ['sometimes', 'required', 'string', 'min:3', 'max:255'],
            'author' => ['sometimes', 'required', 'string', 'min:3', 'max:100'],
            'summary' => ['sometimes', 'required', 'string', 'min:10', 'max:500'],
            'isbn' => [
                'sometimes',
                'required',
                'string',
                'size:13',
                Rule::unique('books', 'isbn')->ignore($book),
            ],
        ];
    }
}
