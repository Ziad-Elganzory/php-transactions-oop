<?php

declare(strict_types=1);

namespace App\Models;

use App\Model;

class Transaction extends Model
{
    public function storeTransaction(array $transactions)
    {
        try{
            foreach ($transactions as $transaction) {
                $stmt = $this->db->prepare('INSERT INTO transactions (transaction_date, check_number, description, amount) VALUES (:transaction_date, :check_number, :description, :amount)');
                $stmt->bindParam(':transaction_date', $transaction['transaction_date']);
                $stmt->bindParam(':check_number', $transaction['check_number']);
                $stmt->bindParam(':description', $transaction['description']);
                $stmt->bindParam(':amount', $transaction['amount']);
                $stmt->execute();
            }
            return true;
        } catch (\Exception $e) {
            echo 'Error storing transactions: ' . $e->getMessage();
            return false;
        }

    }

    public function getTransactionsCsv(string $filePath, ?callable $transactionHandler = null): array
    {
        if (! file_exists($filePath)) {
            throw new \Exception('File "' . $filePath . '" does not exist.');
        }

        $fileParts = pathinfo($filePath);
        if($fileParts['extension'] !== 'csv'){
            throw new \Exception('File must be a CSV');
        }

        $file = fopen($filePath, 'r');
        fgetcsv($file);
        while (($transaction = fgetcsv($file)) !== false) {
            if ($transactionHandler !== null) {
                $transaction = $transactionHandler($transaction);
            }

            $transactions[] = $transaction;
        }

        return $transactions;
    }

    public function getTransactionsDB(): array
    {
        $stmt = $this->db->query('SELECT * FROM transactions');
        $transactions = $stmt->fetchAll();
        return $transactions;
    }

    public function extractTransaction(array $transactionRow): array
    {
        [$date, $checkNumber, $description, $amount] = $transactionRow;

        $amount = (float) str_replace(['$', ','], '', $amount);

        return [
            'date'        => $date,
            'checkNumber' => $checkNumber,
            'description' => $description,
            'amount'      => $amount,
        ];
    }

    public function calculateTotals(array $transactions): array
    {
        $totals = ['netTotal' => 0, 'totalIncome' => 0, 'totalExpense' => 0];

        foreach ($transactions as $transaction) {
            $totals['netTotal'] += $transaction['amount'];

            if ($transaction['amount'] >= 0) {
                $totals['totalIncome'] += $transaction['amount'];
            } else {
                $totals['totalExpense'] += $transaction['amount'];
            }
        }

        return $totals;
    }
}