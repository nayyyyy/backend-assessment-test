<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\DebitCardTransactionFactory;
use Eloquent;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * App\Models\DebitCardTransaction
 *
 * @property int $id
 * @property int $debit_card_id
 * @property int $amount
 * @property string $currency_code
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read \App\Models\DebitCard|null $debitCard
 * @method static \Database\Factories\DebitCardTransactionFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|DebitCardTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DebitCardTransaction newQuery()
 * @method static Builder|DebitCardTransaction onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|DebitCardTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|DebitCardTransaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DebitCardTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DebitCardTransaction whereCurrencyCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DebitCardTransaction whereDebitCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DebitCardTransaction whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DebitCardTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DebitCardTransaction whereUpdatedAt($value)
 * @method static Builder|DebitCardTransaction withTrashed()
 * @method static Builder|DebitCardTransaction withoutTrashed()
 * @mixin Eloquent
 */
class DebitCardTransaction extends Authenticatable
{
    use HasFactory, SoftDeletes;

    // Currencies available
    public const CURRENCY_IDR = 'IDR';
    public const CURRENCY_SGD = 'SGD';
    public const CURRENCY_THB = 'THB';
    public const CURRENCY_VND = 'VND';

    public const CURRENCIES = [
        self::CURRENCY_IDR,
        self::CURRENCY_SGD,
        self::CURRENCY_THB,
        self::CURRENCY_VND,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'debit_card_transactions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'debit_card_id',
        'amount',
        'currency_code',
    ];

    protected $casts = [
        'amount' => 'integer'
    ];

    /**
     * A Debit card transaction belongs to a Debit card
     *
     * @return BelongsTo
     */
    public function debitCard(): BelongsTo
    {
        return $this->belongsTo(DebitCard::class, 'debit_card_id');
    }
}
