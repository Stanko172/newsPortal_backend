<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Article::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $users = User::pluck('id')->toArray();
        $categories = Category::pluck('id')->toArray();
        return [
            'title' => $this->faker->sentence(10),
            'body' => $this->faker->sentence(250),
            'views' => $this->faker->randomDigit(),
            'recommended' => rand(0, 1) == 1,
            'user_id' => $this->faker->randomElement($users),
            'category_id' => $this->faker->randomElement($categories)
        ];
    }
}
