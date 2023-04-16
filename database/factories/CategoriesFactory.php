<?php

namespace Database\Factories;
use App\Models\Categories;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Categories>
 */
class CategoriesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Categories::class;
    public function definition(): array
    {
        $user = User::all()->random();

        return [
            'name' => $this->faker->sentence(2),
            'user_id' => $user->id,
        ];
    }
}
