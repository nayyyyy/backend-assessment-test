<?php

namespace Database\Factories;

use App\Models\Loan;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Loan::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $amount = round($this->faker->numberBetween(500, 10000), 100);

        return [
            'amount' => $amount,
            'terms' => $this->faker->numberBetween(1, 6),
            'outstanding_amount' => $amount,
            'currency_code' => $this->faker->randomElement(Loan::CURRENCIES),
            'processed_at' => now()->toDateString(),
            'status' => Loan::STATUS_DUE,
        ];
    }
}
