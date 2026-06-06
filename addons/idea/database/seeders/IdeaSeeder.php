<?php

namespace Database\Seeders;

use App\Addons\Idea\Models\IdeaTag;
use Illuminate\Database\Seeder;

class IdeaSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['name' => 'Offres', 'slug' => 'offre', 'color' => '2563eb', 'is_public' => true],
        ];

        foreach ($tags as $tag) {
            IdeaTag::firstOrCreate(
                ['slug' => $tag['slug']],
                $tag
            );
        }

        $this->command?->info('Idea tags seeded.');
    }
}
