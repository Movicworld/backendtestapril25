<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    private static array $categories = [
        'Travel',
        'Office Supplies',
        'Meals',
        'Software',
        'Marketing',
        'Utilities'
    ];

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'user_id'    => User::factory(),
            'title'      => $this->faker->sentence(3),
            'amount'     => $this->faker->randomFloat(2, 10, 5000),
            'category'   => $this->faker->randomElement(self::$categories),
        ];
    }
}
