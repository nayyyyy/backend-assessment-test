<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\LoanFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Loan
 *
 * @property int $id
 * @property int $user_id
 * @property int $amount
 * @property int $terms
 * @property int $outstanding_amount
 * @property string $currency_code
 * @property Carbon $processed_at
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Collection|ScheduledRepayment[] $scheduledRepayments
 * @property-read int|null $scheduled_repayments_count
 * @property-read User|null $user
 * @method static LoanFactory factory(...$parameters)
 * @method static Builder|Loan newModelQuery()
 * @method static Builder|Loan newQuery()
 * @method static Builder|Loan query()
 * @method static Builder|Loan whereAmount($value)
 * @method static Builder|Loan whereCreatedAt($value)
 * @method static Builder|Loan whereCurrencyCode($value)
 * @method static Builder|Loan whereDeletedAt($value)
 * @method static Builder|Loan whereId($value)
 * @method static Builder|Loan whereOutstandingAmount($value)
 * @method static Builder|Loan whereProcessedAt($value)
 * @method static Builder|Loan whereStatus($value)
 * @method static Builder|Loan whereTerms($value)
 * @method static Builder|Loan whereUpdatedAt($value)
 * @method static Builder|Loan whereUserId($value)
 * @mixin Eloquent
 */
class Loan extends Model
{
    public const STATUS_DUE = 'due';
    public const STATUS_REPAID = 'repaid';

    public const CURRENCY_SGD = 'SGD';
    public const CURRENCY_VND = 'VND';
    public const CURRENCIES = [
        self::CURRENCY_SGD,
        self::CURRENCY_VND,
    ];

    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'loans';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'amount',
        'terms',
        'outstanding_amount',
        'currency_code',
        'processed_at',
        'status',
    ];

    protected $casts = [
        'user_id' => 'int',
        'amount' => 'int',
        'terms' => 'int',
        'outstanding_amount' => 'int',
        'processed_at' => 'date:Y-m-d',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(static function (self $loan): void {
            $loan->outstanding_amount = $loan->amount;
        });

        static::updating(static function (self $loan): void {
            if ($loan->outstanding_amount <= 0) {
                $loan->status = self::STATUS_REPAID;
            }
        });
    }

    /**
     * A Loan belongs to a User
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * A Loan has many Scheduled Repayments
     *
     * @return HasMany
     */
    public function scheduledRepayments(): HasMany
    {
        return $this->hasMany(ScheduledRepayment::class, 'loan_id');
    }
}
