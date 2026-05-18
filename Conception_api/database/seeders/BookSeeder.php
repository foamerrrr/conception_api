<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;
use RuntimeException;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/books.json');

        if (! is_file($path)) {
            throw new RuntimeException("Fichier introuvable : {$path}");
        }

        $json = file_get_contents($path);
        if ($json === false) {
            throw new RuntimeException("Impossible de lire : {$path}");
        }

        $rows = json_decode($json, true, flags: JSON_THROW_ON_ERROR);

        if (! is_array($rows)) {
            throw new RuntimeException('Le JSON des livres doit être un tableau.');
        }

        Book::query()->delete();

        foreach ($rows as $row) {
            Book::query()->updateOrCreate(
                ['isbn' => $row['isbn']],
                [
                    'title' => $row['title'],
                    'author' => $row['author'],
                    'summary' => $row['summary'],
                ],
            );
        }
    }
}
