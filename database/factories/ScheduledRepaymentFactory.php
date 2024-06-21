<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Loan;
use App\Models\ScheduledRepayment;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduledRepaymentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ScheduledRepayment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'currency_code' => $this->faker->randomElement(Loan::CURRENCIES),
            'due_date' => now()->addMonth()->toDateString(),
            'status' => ScheduledRepayment::STATUS_DUE,
        ];
    }
}
