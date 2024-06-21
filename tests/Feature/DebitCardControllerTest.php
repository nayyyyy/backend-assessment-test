<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Http\Resources\DebitCardResource;
use App\Models\DebitCard;
use App\Models\DebitCardTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class DebitCardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Passport::actingAs($this->user);
    }

    public function testCustomerCanSeeAListOfDebitCards()
    {
        DebitCard::factory(2)->create([
            'user_id' => $this->user->id,
        ]);

        $cards = $this->user->debitCards()->active()->get();
        $assertData = DebitCardResource::collection($cards)->toArray(null);

        $this->getJson(route('debit-cards.index'))
            ->assertOk()
            ->assertJsonFragment($assertData);
    }

    public function testCustomerCannotSeeAListOfDebitCardsOfOtherCustomers()
    {
        /** @var DebitCard $card */
        $card = DebitCard::factory()->create([
            'user_id' => 2,
        ]);

        $this->getJson(route('debit-cards.index'))
            ->assertOk()
            ->assertJsonFragment([]);

        $this->assertDatabaseHas('debit_cards', $card->only(["user_id", "number", "type"]));
    }

    public function testCustomerCanCreateADebitCard()
    {
        $request = [
            "type" => $this->faker->creditCardType()
        ];

        $this->postJson(route('debit-cards.store'), $request)
            ->assertCreated();

        $this->assertDatabaseHas('debit_cards', [
            'type' => $request['type'],
            'expiration_date' => now()->addYear()
        ]);
    }

    public function testCustomerCanSeeASingleDebitCardDetails()
    {
        /** @var DebitCard $card */
        $card = DebitCard::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $this->getJson(route('debit-cards.show', $card))
            ->assertOk()
            ->assertJsonFragment($card->only('id', 'number', 'type'));
    }

    public function testCustomerCannotSeeASingleDebitCardDetails()
    {
        DebitCard::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $this->getJson(route('debit-cards.show', 4))
            ->assertNotFound();
    }

    // NEW TEST
    public function testCustomerCannotSeeASingleDebitCardDetailsOtherUser()
    {
        /** @var DebitCard $card */
        $card = DebitCard::factory()->create([
            'user_id' => 2,
        ]);

        $this->getJson(route('debit-cards.show', $card->id))
            ->assertForbidden();
    }

    public function testCustomerCanActivateADebitCard()
    {
        /** @var DebitCard $card */
        $card = DebitCard::factory()->expired()->create(['user_id' => $this->user->id]);

        $this->putJson(route('debit-cards.update', $card), ['is_active' => true])
            ->assertOk()
            ->assertJsonFragment(['is_active' => true]);

        $this->assertDatabaseHas('debit_cards', [
            'id' => $card->id,
            'disabled_at' => null
        ]);
    }

    public function testCustomerCanDeactivateADebitCard()
    {
        /** @var DebitCard $card */
        $card = DebitCard::factory()->active()->create(['user_id' => $this->user->id]);

        $this->putJson(route('debit-cards.update', $card), ['is_active' => false])
            ->assertOk()
            ->assertJsonFragment(['is_active' => false]);

        $this->assertDatabaseHas('debit_cards', [
            'id' => $card->id,
            'disabled_at' => now()
        ]);
    }

    public function testCustomerCannotUpdateADebitCardWithWrongValidation()
    {
        /** @var DebitCard $card */
        $card = DebitCard::factory()->active()->create(['user_id' => $this->user->id]);

        $this->putJson(route('debit-cards.update', $card), ['is_activ' => false])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['is_active']);
    }

    public function testCustomerCanDeleteADebitCard()
    {
        /** @var DebitCard $card */
        $card = DebitCard::factory()->active()->create(['user_id' => $this->user->id]);

        $this->deleteJson(route('debit-cards.destroy', $card))
            ->assertNoContent();

        $this->assertSoftDeleted('debit_cards', $card->only('id', 'number', 'type'));
    }

    public function testCustomerCannotDeleteADebitCardWithTransaction()
    {
        /** @var DebitCard $card */
        $card = DebitCard::factory()->active()->create(['user_id' => $this->user->id]);

        DebitCardTransaction::factory(3)->create(['debit_card_id' => $card->id]);

        $this->deleteJson(route('debit-cards.destroy', $card))
            ->assertForbidden();
    }
}
