<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
class BlogPostFactory extends Factory
{
    public function definition()
    {
        return [
            'company_id' => null,
            'title' => ucfirst($this->faker->sentence(4)),
            'content' => $this->faker->paragraphs(3, true),
            'image_path' => null,
            'published_at' => now(),
            'created_by' => null,
        ];
    }
}
