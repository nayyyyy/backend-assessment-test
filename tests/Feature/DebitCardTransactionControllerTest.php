<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Http\Resources\DebitCardTransactionResource;
use App\Models\DebitCard;
use App\Models\DebitCardTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class DebitCardTransactionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected DebitCard $debitCard;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->debitCard = DebitCard::factory()->create([
            'user_id' => $this->user->id
        ]);
        Passport::actingAs($this->user);
    }

    public function testCustomerCanSeeAListOfDebitCardTransactions()
    {
        $request = [
            'debit_card_id' => $this->debitCard->id
        ];

        $transactions = DebitCardTransaction::factory(2)->create([
            'debit_card_id' => $this->debitCard->id
        ]);

        $assertedData = DebitCardTransactionResource::collection($transactions)->toArray(null);

        $this->getJson(route('debit-card-transactions.index', $request))
            ->assertOk()
            ->assertJson($assertedData);
    }

    public function testCustomerCannotSeeAListOfDebitCardTransactionsOfOtherCustomerDebitCard()
    {
        /** @var DebitCard $otherDebitCard */
        $otherDebitCard = DebitCard::factory()->active()->create([
            'user_id' => 2
        ]);

        $request = [
            'debit_card_id' => $otherDebitCard->id
        ];

        DebitCardTransaction::factory(2)->create([
            'debit_card_id' => $otherDebitCard->id
        ]);

        $this->getJson(route('debit-card-transactions.index', $request))
            ->assertForbidden();
    }

    public function testCustomerCanCreateADebitCardTransaction()
    {
        $request = [
            'debit_card_id' => $this->debitCard->id,
            'amount' => $this->faker->numberBetween(50, 1000),
            'currency_code' => $this->faker->randomElement(DebitCardTransaction::CURRENCIES),
        ];

        $this->postJson(route('debit-card-transactions.store', $request))
            ->assertCreated();

        $this->assertDatabaseHas('debit_card_transactions', $request);
    }

    public function testCustomerCannotCreateADebitCardTransactionToOtherCustomerDebitCard()
    {
        /** @var DebitCard $otherDebitCard */
        $otherDebitCard = DebitCard::factory()->active()->create([
            'user_id' => 2
        ]);

        $request = [
            'debit_card_id' => $otherDebitCard->id,
            'amount' => $this->faker->numberBetween(50, 1000),
            'currency_code' => $this->faker->randomElement(DebitCardTransaction::CURRENCIES),
        ];

        $this->postJson(route('debit-card-transactions.store', $request))
            ->assertForbidden();
    }

    public function testCustomerCanSeeADebitCardTransaction()
    {
        $transaction = DebitCardTransaction::factory()->create([
            'debit_card_id' => $this->debitCard->id,
        ]);

        $this->getJson(route('debit-card-transactions.show', $transaction))
            ->assertOk()
            ->assertJson(DebitCardTransactionResource::make($transaction)->toArray(null));
    }

    public function testCustomerCannotSeeADebitCardTransactionAttachedToOtherCustomerDebitCard()
    {
        /** @var DebitCard $otherDebitCard */
        $otherDebitCard = DebitCard::factory()->active()->create([
            'user_id' => 2
        ]);

        $transaction = DebitCardTransaction::factory()->create([
            'debit_card_id' => $otherDebitCard->id,
        ]);

        $this->getJson(route('debit-card-transactions.show', $transaction))
            ->assertForbidden();
    }
}
