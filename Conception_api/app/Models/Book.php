<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'title',
    'author',
    'summary',
    'isbn',
])]
class Book extends Model
{
    /**
     * Route binding : les URLs utilisent l'ISBN (présent dans le JSON) plutôt que l'id interne.
     */
    public function getRouteKeyName(): string
    {
        return 'isbn';
    }
}
