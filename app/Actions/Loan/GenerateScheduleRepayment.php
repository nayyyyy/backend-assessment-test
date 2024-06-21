<?php

declare(strict_types=1);

namespace App\Actions\Loan;

use App\Abstracts\AbstractAction;
use App\Models\Loan;
use App\Models\ScheduledRepayment;
use Carbon\Carbon;

class GenerateScheduleRepayment extends AbstractAction
{
    private Loan $loan;

    public function __construct(Loan $loan)
    {
        $this->loan = $loan;
    }

    public function handle(): void
    {
        $schedules = [];
        $outstandingAmount = $this->loan->amount;
        $amount = (int)floor($this->loan->amount / $this->loan->terms);

        for ($i = 1; $i <= $this->loan->terms; $i++) {
            $date = $this->loan->processed_at->addMonths($i);
            $schedules[] = $this->createScheduleRepayment(
                $i != $this->loan->terms ? $amount : $outstandingAmount,
                $date
            );
            $outstandingAmount -= $amount;
        }

        $this->loan->scheduledRepayments()->saveMany($schedules);
    }

    private function createScheduleRepayment(int $amount, Carbon $dueDate): ScheduledRepayment
    {
        $schedule = new ScheduledRepayment();

        $schedule->amount = $amount;
        $schedule->outstanding_amount = $amount;
        $schedule->currency_code = $this->loan->currency_code;
        $schedule->due_date = $dueDate->toDateString();
        $schedule->status = ScheduledRepayment::STATUS_DUE;

        return $schedule;
    }
}
