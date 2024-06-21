<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\DebitCardFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Carbon;

/**
 * App\Models\DebitCard
 *
 * @property int $id
 * @property int $user_id
 * @property int $number
 * @property string $type
 * @property string $expiration_date
 * @property Carbon|null $disabled_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection|\App\Models\DebitCardTransaction[] $debitCardTransactions
 * @property-read int|null $debit_card_transactions_count
 * @property-read bool $is_active
 * @property-read \App\Models\User|null $user
 * @method static Builder|DebitCard active()
 * @method static \Database\Factories\DebitCardFactory factory(...$parameters)
 * @method static Builder|DebitCard newModelQuery()
 * @method static Builder|DebitCard newQuery()
 * @method static \Illuminate\Database\Query\Builder|DebitCard onlyTrashed()
 * @method static Builder|DebitCard query()
 * @method static Builder|DebitCard whereCreatedAt($value)
 * @method static Builder|DebitCard whereDeletedAt($value)
 * @method static Builder|DebitCard whereDisabledAt($value)
 * @method static Builder|DebitCard whereExpirationDate($value)
 * @method static Builder|DebitCard whereId($value)
 * @method static Builder|DebitCard whereNumber($value)
 * @method static Builder|DebitCard whereType($value)
 * @method static Builder|DebitCard whereUpdatedAt($value)
 * @method static Builder|DebitCard whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|DebitCard withTrashed()
 * @method static \Illuminate\Database\Query\Builder|DebitCard withoutTrashed()
 * @mixin Eloquent
 */
class DebitCard extends Authenticatable
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'debit_cards';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'number',
        'type',
        'expiration_date',
        'disabled_at',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'disabled_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'disabled_at' => 'datetime:Y-m-d H:i:s',
    ];


    /**
     * A Debit Card belongs to a user
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * A Debit Card has many debit card transactions
     *
     * @return HasMany
     */
    public function debitCardTransactions(): HasMany
    {
        return $this->hasMany(DebitCardTransaction::class, 'debit_card_id');
    }

    /**
     * Scope active debit cards
     *
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('disabled_at');
    }

    /**
     * Convert disabled_at in boolean attribute
     *
     * @return bool
     */
    public function getIsActiveAttribute(): bool
    {
        return is_null($this->disabled_at);
    }
}
