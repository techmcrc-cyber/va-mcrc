<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Spiritual Retreats',
                'slug' => 'spiritual-retreats',
                'description' => 'Retreats focused on spiritual growth and renewal',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Yoga & Meditation',
                'slug' => 'yoga-meditation',
                'description' => 'Retreats focused on yoga, meditation, and mindfulness practices',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Silent Retreats',
                'slug' => 'silent-retreats',
                'description' => 'Retreats focused on silence and contemplation',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Wellness Retreats',
                'slug' => 'wellness-retreats',
                'description' => 'Retreats focused on health and wellness',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Youth Retreats',
                'slug' => 'youth-retreats',
                'description' => 'Retreats designed specifically for young people',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Couples Retreats',
                'slug' => 'couples-retreats',
                'description' => 'Retreats designed for couples to grow together',
                'is_active' => true,
                'sort_order' => 6,
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
