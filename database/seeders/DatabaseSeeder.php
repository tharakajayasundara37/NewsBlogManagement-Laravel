<?php

namespace Database\Seeders;

use App\Models\{Category, Post, User};
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $data = json_decode(file_get_contents(database_path('seed-data/legacy-content.json')), true, flags: JSON_THROW_ON_ERROR);

        $admin = User::updateOrCreate(['email' => env('ADMIN_EMAIL', 'admin@newshub.local')], [
            'name' => 'NewsHub Administrator', 'password' => env('ADMIN_PASSWORD', 'change-this-password'), 'role' => 'admin',
        ]);

        $categoryIds = [];
        foreach ($data['categories'] as $item) {
            $category = Category::updateOrCreate(['legacy_id' => $item['id']], [
                'category_name' => $item['category_name'],
                'slug' => $item['slug'] ?: \Illuminate\Support\Str::slug($item['category_name']),
                'description' => $item['description'], 'status' => $item['status'] ?? 'active',
            ]);
            $categoryIds[$item['id']] = (string) $category->_id;
        }

        foreach ($data['posts'] as $item) {
            Post::updateOrCreate(['legacy_id' => $item['id']], [
                'title' => $item['title'], 'slug' => \Illuminate\Support\Str::slug($item['title']).'-'.$item['id'],
                'content' => $item['content'], 'image' => $item['image'],
                'category_id' => $categoryIds[$item['category_id']] ?? null, 'author_id' => (string) $admin->_id,
                'status' => $item['status'] ?? 'draft', 'views' => 0,
                'published_at' => $item['published_date'] ?: $item['created_date'],
            ]);
        }
    }
}
