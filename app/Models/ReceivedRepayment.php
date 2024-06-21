<?php

declare(strict_types=1);

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\ReceivedRepayment
 *
 * @property int $id
 * @property int $loan_id
 * @property int $amount
 * @property string $currency_code
 * @property Carbon $received_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\Loan|null $loan
 * @method static Builder|ReceivedRepayment newModelQuery()
 * @method static Builder|ReceivedRepayment newQuery()
 * @method static Builder|ReceivedRepayment query()
 * @method static Builder|ReceivedRepayment whereAmount($value)
 * @method static Builder|ReceivedRepayment whereCreatedAt($value)
 * @method static Builder|ReceivedRepayment whereCurrencyCode($value)
 * @method static Builder|ReceivedRepayment whereDeletedAt($value)
 * @method static Builder|ReceivedRepayment whereId($value)
 * @method static Builder|ReceivedRepayment whereLoanId($value)
 * @method static Builder|ReceivedRepayment whereReceivedAt($value)
 * @method static Builder|ReceivedRepayment whereUpdatedAt($value)
 * @mixin Eloquent
 */
class ReceivedRepayment extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'received_repayments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'amount',
        'currency_code',
        'received_at'
    ];

    protected $casts = [
        'amount' => 'integer',
        'received_at' => 'date:Y-m-d',
    ];

    /**
     * A Received Repayment belongs to a Loan
     *
     * @return BelongsTo
     */
    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class, 'loan_id');
    }
}
