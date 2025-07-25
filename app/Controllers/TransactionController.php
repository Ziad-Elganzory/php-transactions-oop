<?php

declare(strict_types = 1);

namespace App\Controllers;

use App\Models\Transaction;
use App\View;
use Exception;

class TransactionController
{
    private Transaction $transactionModel;
    public function __construct()
    {
        $this->transactionModel = new Transaction();
    }
    public function index(): View
    {
        try {
            $transactions = $this->transactionModel->getTransactionsDB();
            if (empty($transactions)) {
                throw new Exception('No transactions found.');
            }

            $totals = $this->transactionModel->calculateTotals($transactions);
            return View::make('transactions/index', [
                'transactions' => $transactions,
                'totals' => $totals,
            ]);
        } catch (Exception $e) {
            throw new Exception('Error fetching transactions: ' . $e->getMessage());
        }
    }

    public function upload(): View
    {
        return View::make('transactions/upload');
    }

    public function store()
    {
        $filePath = STORAGE_PATH . '/' . $_FILES['transactions']['name'];
        $fileName = $_FILES['transactions']['name'];
        move_uploaded_file($_FILES['transactions']['tmp_name'], $filePath);

        $transactions = $this->transactionModel->getTransactionsCsv($filePath, function ($transaction) {
            return [
                'transaction_date' => date('Y-m-d H:i:s', strtotime($transaction[0])),
                'check_number' => $transaction[1],
                'description' => $transaction[2],
                'amount' => (float) str_replace(['$', ','], '', $transaction[3]),
            ];
        });

        $storeTransactions = $this->transactionModel->storeTransaction($transactions);
        header('Location: /transactions');
        exit;
    }
}
