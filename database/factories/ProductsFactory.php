<?php

namespace Database\Factories;
use App\Models\Categories;
use App\Models\Products;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Products>
 */
class ProductsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Products::class;
    public function definition(): array
    {
        $category = Categories::all()->random();
        // $fakerFileName = $this->faker->image(
        //     storage_path("app/public"),
        //     800,
        //     600
        // );
        return [

            'name' => $this->faker->sentence(2),
            'description' => $this->faker->text(),
            'price' => $this->faker->randomElement([9.99,25.99,99.99]),
            'stock' => $this->faker->randomElement([1,50,100]),
            'image' => $this->faker->imageUrl(800,600),
            'category_id' => $category->id,

        ];
    }
}
