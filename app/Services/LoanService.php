<?php

declare(strict_types=1);

namespace App\Services;

use App\Actions\Loan\GenerateScheduleRepayment;
use App\Actions\Loan\PaidLoan;
use App\Actions\Loan\SaveLoan;
use App\Actions\Loan\SaveReceivedRepayment;
use App\Models\Loan;
use App\Models\ReceivedRepayment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LoanService
{
    /**
     * Create a Loan
     *
     * @param User $user
     * @param int $amount
     * @param string $currencyCode
     * @param int $terms
     * @param string $processedAt
     *
     * @return Loan
     */
    public function createLoan(User $user, int $amount, string $currencyCode, int $terms, string $processedAt): Loan
    {
        return DB::transaction(function () use ($user, $amount, $currencyCode, $terms, $processedAt) {
            /** @var Loan $loan */
            $loan = dispatch_now(
                new SaveLoan(
                    new Loan(),
                    [
                        "amount" => $amount,
                        "terms" => $terms,
                        "currency_code" => $currencyCode,
                        "processed_at" => $processedAt,
                        "status" => Loan::STATUS_DUE
                    ],
                    $user),
            );

            dispatch_now(new GenerateScheduleRepayment($loan));

            return $loan;
        });
    }

    /**
     * Repay Scheduled Repayments for a Loan
     *
     * @param Loan $loan
     * @param int $amount
     * @param string $currencyCode
     * @param string $receivedAt
     *
     * @return ReceivedRepayment
     */
    public function repayLoan(Loan $loan, int $amount, string $currencyCode, string $receivedAt): ReceivedRepayment
    {
        return DB::transaction(function () use ($loan, $amount, $currencyCode, $receivedAt) {
            $loan = dispatch_now(
                new SaveLoan(
                    $loan, ['outstanding_amount' => $loan->outstanding_amount - $amount]
                )
            );

            $receivedRepayment = dispatch_now(
                new SaveReceivedRepayment($loan, new ReceivedRepayment(), $amount, $currencyCode, $receivedAt)
            );

            dispatch_now(new PaidLoan($loan, $receivedRepayment));

            return $receivedRepayment;
        });
    }
}
