<?php

declare(strict_types=1);

namespace App\Actions\Loan;

use App\Abstracts\AbstractAction;
use App\Models\Loan;
use App\Models\ReceivedRepayment;
use App\Models\ScheduledRepayment;

class SaveReceivedRepayment extends AbstractAction
{
    private Loan $loan;
    private ReceivedRepayment $receivedRepayment;
    private int $amount;
    private string $currencyCode;
    private string $receivedAt;

    public function __construct(
        Loan              $loan,
        ReceivedRepayment $receivedRepayment,
        int               $amount,
        string            $currencyCode,
        string            $receivedAt
    )
    {
        $this->loan = $loan;
        $this->receivedRepayment = $receivedRepayment;
        $this->amount = $amount;
        $this->currencyCode = $currencyCode;
        $this->receivedAt = $receivedAt;
    }

    public function handle()
    {
        $this->receivedRepayment->amount = $this->amount;
        $this->receivedRepayment->currency_code = $this->currencyCode;
        $this->receivedRepayment->received_at = $this->receivedAt;
        $this->receivedRepayment->loan()->associate($this->loan);
        $this->receivedRepayment->save();

        return $this->receivedRepayment;
    }
}
