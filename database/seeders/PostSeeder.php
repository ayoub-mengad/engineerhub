<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        
        $samplePosts = [
            [
                'content' => '🏗️ Just finished designing a new suspension bridge! The engineering challenges were incredible, especially dealing with wind load calculations. Anyone else working on infrastructure projects?',
                'visibility' => 'public'
            ],
            [
                'content' => '💡 Quick tip for fellow civil engineers: When calculating concrete mix ratios, always account for local climate conditions. Temperature and humidity can significantly affect curing time and strength.',
                'visibility' => 'public'
            ],
            [
                'content' => '🔧 Debugging a complex structural analysis software issue today. Sometimes the best solutions come from stepping back and reviewing the fundamentals. #Engineering #StructuralAnalysis',
                'visibility' => 'friends_only'
            ],
            [
                'content' => '📊 Completed a seismic risk assessment for a high-rise building project. The data shows we need additional dampers on floors 15-20. Safety first! 🏢',
                'visibility' => 'public'
            ],
            [
                'content' => '🌉 Visited the Golden Gate Bridge today and marveled at the engineering marvel. The cable tension calculations must have been mind-boggling in the 1930s without modern computers!',
                'visibility' => 'public'
            ],
            [
                'content' => '⚡ Working on a renewable energy project - designing wind turbine foundations. The soil analysis is crucial for determining the right foundation depth. #GreenEnergy #SustainableDesign',
                'visibility' => 'friends_only'
            ],
            [
                'content' => '🎓 Mentoring young engineers today reminded me why I love this profession. Their fresh perspectives on old problems are always inspiring! #Mentorship #Engineering',
                'visibility' => 'public'
            ],
            [
                'content' => '🏭 Factory expansion project update: We\'ve successfully integrated new safety protocols while maintaining production efficiency. Teamwork makes the dream work! 👷‍♀️👷‍♂️',
                'visibility' => 'public'
            ]
        ];

        foreach ($samplePosts as $postData) {
            Post::create([
                'user_id' => $users->random()->id,
                'content' => $postData['content'],
                'visibility' => $postData['visibility'],
                'created_at' => now()->subDays(rand(0, 30))->subHours(rand(0, 23))
            ]);
        }
    }
}
