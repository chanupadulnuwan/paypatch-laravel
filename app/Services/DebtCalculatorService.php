<?php

namespace App\Services;

// DebtCalculatorService
// ----------------------
// Takes an array of member balances (userId => amount)
// where positive = they are OWED money, negative = they OWE money.
//
// Returns the minimum list of transactions needed to settle all debts.
// Example input:  [1 => 100.00, 2 => -60.00, 3 => -40.00]
// Example output: [ ['from'=>2,'to'=>1,'amount'=>60], ['from'=>3,'to'=>1,'amount'=>40] ]

class DebtCalculatorService
{
    public function simplify(array $balances): array
    {
        $transactions = [];

        // Separate into people who owe money (debtors) and people who are owed (creditors)
        $debtors   = [];  // ['id' => X, 'amount' => positive number they owe]
        $creditors = [];  // ['id' => X, 'amount' => positive number owed to them]

        foreach ($balances as $userId => $balance) {
            $balance = round($balance, 2);
            if ($balance < 0) {
                $debtors[]   = ['id' => $userId, 'amount' => abs($balance)];
            } elseif ($balance > 0) {
                $creditors[] = ['id' => $userId, 'amount' => $balance];
            }
            // balance == 0 means they're already settled, skip
        }

        // Greedy match: take the biggest debtor and biggest creditor each round
        $i = 0; // debtor pointer
        $j = 0; // creditor pointer

        while ($i < count($debtors) && $j < count($creditors)) {
            $debtor   = &$debtors[$i];
            $creditor = &$creditors[$j];

            // The payment is the smaller of the two amounts
            $payment = min($debtor['amount'], $creditor['amount']);
            $payment = round($payment, 2);

            $transactions[] = [
                'from'   => $debtor['id'],
                'to'     => $creditor['id'],
                'amount' => $payment,
            ];

            // Reduce both sides by the payment
            $debtor['amount']   -= $payment;
            $creditor['amount'] -= $payment;

            // If the debtor is fully paid off, move to the next debtor
            if ($debtor['amount'] < 0.01) {
                $i++;
            }

            // If the creditor is fully paid back, move to the next creditor
            if ($creditor['amount'] < 0.01) {
                $j++;
            }
        }

        return $transactions;
    }
}
