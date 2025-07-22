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

    public function getTransactions(string $filePath, ?callable $transactionHandler = null): array
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
}