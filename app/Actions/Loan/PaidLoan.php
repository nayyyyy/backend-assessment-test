<?php

declare(strict_types=1);

namespace App\Actions\Loan;

use App\Abstracts\AbstractAction;
use App\Models\Loan;
use App\Models\ReceivedRepayment;
use App\Models\ScheduledRepayment;

class PaidLoan extends AbstractAction
{
    private Loan $loan;
    private ReceivedRepayment $receivedRepayment;

    public function __construct(
        Loan              $loan,
        ReceivedRepayment $receivedRepayment
    )
    {
        $this->loan = $loan;
        $this->receivedRepayment = $receivedRepayment;
    }

    public function handle()
    {
        $remainingBalance = $this->receivedRepayment->amount;

        $schedule = $this->loan->scheduledRepayments()
            ->notRepaid()
            ->get();

        /** @var ScheduledRepayment $scheduledRepayment */
        foreach ($schedule as $i => $scheduledRepayment) {
            if ($remainingBalance <= 0) {
                break;
            }

            if ($remainingBalance >= $scheduledRepayment->outstanding_amount) {
                $remainingBalance -= $scheduledRepayment->amount;
                $schedule[$i] = $this->saveRepaid($scheduledRepayment);
            } else {
                $schedule[$i] = $this->savePartial($scheduledRepayment, $remainingBalance);
                $remainingBalance = 0;
            }
        }
    }

    private function saveRepaid(ScheduledRepayment $scheduledRepayment): ScheduledRepayment
    {
        $scheduledRepayment->outstanding_amount = 0;
        $scheduledRepayment->status = ScheduledRepayment::STATUS_REPAID;

        $scheduledRepayment->save();

        return $scheduledRepayment;
    }

    private function savePartial(ScheduledRepayment $scheduledRepayment, int $remainingBalance): ScheduledRepayment
    {
        $scheduledRepayment->outstanding_amount -= $remainingBalance;
        $scheduledRepayment->status = ScheduledRepayment::STATUS_PARTIAL;

        $scheduledRepayment->save();

        return $scheduledRepayment;
    }
}
