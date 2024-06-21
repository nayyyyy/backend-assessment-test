<?php

declare(strict_types=1);

namespace App\Actions\Loan;

use App\Abstracts\AbstractAction;
use App\Models\Loan;
use App\Models\User;
use Carbon\Carbon;

class SaveLoan extends AbstractAction
{
    private ?User $user;
    private Loan $loan;
    private array $data;

    public function __construct(
        Loan  $loan,
        array $data,
        ?User $user = null
    )
    {
        $this->user = $user;
        $this->loan = $loan;
        $this->data = $data;
    }

    public function handle(): Loan
    {
        $this->loan->fill($this->data);

        if ($this->user) {
            $this->loan->user()->associate($this->user);
        }

        $this->loan->save();

        return $this->loan;
    }
}
