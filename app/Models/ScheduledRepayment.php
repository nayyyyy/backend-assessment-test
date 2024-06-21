<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ScheduledRepaymentFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\ScheduledRepayment
 *
 * @property int $id
 * @property int $loan_id
 * @property int $amount
 * @property int $outstanding_amount
 * @property string $currency_code
 * @property Carbon $due_date
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Loan|null $loan
 * @method static ScheduledRepaymentFactory factory(...$parameters)
 * @method static Builder|ScheduledRepayment newModelQuery()
 * @method static Builder|ScheduledRepayment newQuery()
 * @method static Builder|ScheduledRepayment query()
 * @method static Builder|ScheduledRepayment whereAmount($value)
 * @method static Builder|ScheduledRepayment whereCreatedAt($value)
 * @method static Builder|ScheduledRepayment whereCurrencyCode($value)
 * @method static Builder|ScheduledRepayment whereDeletedAt($value)
 * @method static Builder|ScheduledRepayment whereDueDate($value)
 * @method static Builder|ScheduledRepayment whereId($value)
 * @method static Builder|ScheduledRepayment whereLoanId($value)
 * @method static Builder|ScheduledRepayment whereOutstandingAmount($value)
 * @method static Builder|ScheduledRepayment whereStatus($value)
 * @method static Builder|ScheduledRepayment whereUpdatedAt($value)
 * @mixin Eloquent
 */
class ScheduledRepayment extends Model
{
    use HasFactory;

    public const STATUS_DUE = 'due';
    public const STATUS_PARTIAL = 'partial';
    public const STATUS_REPAID = 'repaid';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'scheduled_repayments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'amount',
        'outstanding_amount',
        'currency_code',
        'due_date',
        'status',
    ];

    protected $casts = [
        'loan_id' => 'int',
        'amount' => 'int',
        'outstanding_amount' => 'int',
        'due_date' => 'date:Y-m-d',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(static function (self $scheduledRepayement) {
            if ($scheduledRepayement->status === self::STATUS_REPAID) {
                /** @var Loan $loan */
                $loan = $scheduledRepayement->loan()->firstOrFail();
                $loan->outstanding_amount -= $scheduledRepayement->amount;

                $scheduledRepayement->outstanding_amount = 0;
                $loan->save();
            } else {
                $scheduledRepayement->outstanding_amount = $scheduledRepayement->amount;
            }
        });
    }

    /**
     * A Scheduled Repayment belongs to a Loan
     *
     * @return BelongsTo
     */
    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class, 'loan_id');
    }

    public function scopeNotRepaid(Builder $query): Builder
    {
        return $query->where('status', '!=', self::STATUS_REPAID);
    }
}
