<?php

namespace App\Libraries;

use App\Models\PrefixModel;
use App\Models\TransactionModel;
use App\Models\UserModel;

class TransferService
{
    private UserModel $userModel;
    private PrefixModel $prefixModel;
    private TransactionModel $transactionModel;
    private TransferFeeService $transferFeeService;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->prefixModel = new PrefixModel();
        $this->transactionModel = new TransactionModel();
        $this->transferFeeService = new TransferFeeService();
    }

    public function transferMultiple(array $sender, string $receiverInput, int $totalAmount, bool $includeWithdrawalFee = false): array
    {
        $receiverPhones = $this->parseReceiverPhones($receiverInput);

        if ($totalAmount <= 0) {
            throw new \InvalidArgumentException('Le montant total doit être supérieur à zéro.');
        }

        if ($receiverPhones === []) {
            throw new \InvalidArgumentException('Veuillez saisir au moins un numéro destinataire.');
        }

        if (in_array($sender['phone_number'], $receiverPhones, true)) {
            throw new \InvalidArgumentException("L'expéditeur ne peut pas figurer parmi les destinataires.");
        }

        $receiverCount = count($receiverPhones);
        $parts = $this->splitAmountExactly($totalAmount, $receiverCount);
        $preparedReceivers = [];
        $totalDebit = 0;

        foreach ($receiverPhones as $index => $receiverPhone) {
            $part = $parts[$index];
            $operator = $this->prefixModel->findOperatorByPhone($receiverPhone);

            if (!$operator) {
                throw new \RuntimeException("Opérateur introuvable pour le numéro {$receiverPhone}.");
            }

            $receiverUser = $this->userModel->findByPhone($receiverPhone);
            $isExternal = (int) ($operator['is_own_operator'] ?? 0) === 0;

            if (!$isExternal && !$receiverUser) {
                throw new \RuntimeException("Destinataire interne introuvable pour le numéro {$receiverPhone}.");
            }

            $transferFee = $this->transferFeeService->calculate($part, $receiverPhone);
            $withdrawalFee = $includeWithdrawalFee ? $this->calculateWithdrawalFee($part) : 0;
            $savingsPercent = (int) ($receiverUser['savings_percent'] ?? 0);
            $savingsAmount = $receiverUser ? (int) floor(($part * $savingsPercent) / 100) : 0;
            $creditedBalance = $part - $savingsAmount + $withdrawalFee;
            $lineTotal = $part + $withdrawalFee + $transferFee['total_transfer_fee'];

            $totalDebit += $lineTotal;
            $preparedReceivers[] = [
                'phone' => $receiverPhone,
                'part' => $part,
                'operator' => $operator,
                'user' => $receiverUser,
                'is_external' => $isExternal,
                'base_fee' => $transferFee['base_fee'],
                'promo_percent' => $transferFee['promo_percent'],
                'promo_amount' => $transferFee['promo_amount'],
                'base_fee_after_promo' => $transferFee['base_fee_after_promo'],
                'external_commission' => $transferFee['external_commission'],
                'withdrawal_fee_included' => $withdrawalFee,
                'total_fee' => $transferFee['total_transfer_fee'] + $withdrawalFee,
                'line_total' => $lineTotal,
                'savings_percent' => $savingsPercent,
                'savings_amount' => $savingsAmount,
                'credited_balance' => $creditedBalance,
            ];
        }

        if (($sender['balance'] ?? 0) < $totalDebit) {
            throw new \RuntimeException("Solde insuffisant. Total à débiter : {$totalDebit} Ar.");
        }

        $db = db_connect();
        $db->transBegin();

        try {
            if (!$this->userModel->debit($sender['id'], $totalDebit)) {
                throw new \RuntimeException("Impossible de débiter le compte de l'expéditeur.");
            }

            $batchReference = $this->generateBatchReference();
            $transactions = [];

            foreach ($preparedReceivers as $receiver) {
                if (!$receiver['is_external']) {
                    if (!$this->userModel->credit($receiver['user']['id'], $receiver['credited_balance'])) {
                        throw new \RuntimeException("Impossible de créditer le destinataire {$receiver['phone']}.");
                    }

                    if ($receiver['savings_amount'] > 0 && !$this->userModel->creditSavings($receiver['user']['id'], $receiver['savings_amount'])) {
                        throw new \RuntimeException("Impossible d'enregistrer l'épargne du destinataire {$receiver['phone']}.");
                    }
                }

                $transactionId = $this->transactionModel->createTransferTransaction([
                    'user_id' => $sender['id'],
                    'operation_type_id' => 3,
                    'receiver_user_id' => $receiver['user']['id'] ?? null,
                    'receiver_phone' => $receiver['phone'],
                    'receiver_operator_id' => $receiver['operator']['id'],
                    'amount' => $receiver['part'],
                    'base_fee' => $receiver['base_fee'],
                    'promo_percent' => $receiver['promo_percent'],
                    'promo_amount' => $receiver['promo_amount'],
                    'base_fee_after_promo' => $receiver['base_fee_after_promo'],
                    'external_commission' => $receiver['external_commission'],
                    'included_withdrawal_fee' => $receiver['withdrawal_fee_included'],
                    'savings_percent' => $receiver['savings_percent'],
                    'savings_amount' => $receiver['savings_amount'],
                    'fee' => $receiver['total_fee'],
                    'total_fee' => $receiver['total_fee'],
                    'withdrawal_fee_included' => $includeWithdrawalFee ? 1 : 0,
                    'is_external' => $receiver['is_external'] ? 1 : 0,
                    'batch_reference' => $batchReference,
                    'status' => 'completed',
                ]);

                if (!$transactionId) {
                    throw new \RuntimeException("Impossible d'enregistrer la transaction pour {$receiver['phone']}.");
                }

                $transactions[] = [
                    'transaction_id' => $transactionId,
                    'receiver_phone' => $receiver['phone'],
                    'receiver_operator' => $receiver['operator']['name'] ?? null,
                    'amount' => $receiver['part'],
                    'withdrawal_fee_included' => $receiver['withdrawal_fee_included'],
                    'base_fee' => $receiver['base_fee'],
                    'promo_percent' => $receiver['promo_percent'],
                    'promo_amount' => $receiver['promo_amount'],
                    'base_fee_after_promo' => $receiver['base_fee_after_promo'],
                    'external_commission' => $receiver['external_commission'],
                    'savings_percent' => $receiver['savings_percent'],
                    'savings_amount' => $receiver['savings_amount'],
                    'credited_balance' => $receiver['credited_balance'],
                    'total_fee' => $receiver['total_fee'],
                    'line_total' => $receiver['line_total'],
                    'is_external' => $receiver['is_external'],
                ];
            }

            if ($db->transStatus() === false) {
                throw new \RuntimeException('La transaction SQL a échoué.');
            }

            $db->transCommit();

            return [
                'batch_reference' => $batchReference,
                'receiver_count' => $receiverCount,
                'total_amount' => $totalAmount,
                'total_debit' => $totalDebit,
                'transactions' => $transactions,
            ];
        } catch (\Throwable $e) {
            $db->transRollback();
            throw $e;
        }
    }

    public function splitAmountExactly(int $totalAmount, int $receiverCount): array
    {
        if ($receiverCount <= 0) {
            throw new \InvalidArgumentException('Le nombre de destinataires doit être supérieur à zéro.');
        }

        $baseAmount = intdiv($totalAmount, $receiverCount);
        $remainder = $totalAmount % $receiverCount;
        $parts = [];

        for ($index = 0; $index < $receiverCount; $index++) {
            $parts[] = $baseAmount + ($index < $remainder ? 1 : 0);
        }

        if (array_sum($parts) !== $totalAmount) {
            throw new \RuntimeException('La répartition du montant est invalide.');
        }

        return $parts;
    }

    private function parseReceiverPhones(string $receiverInput): array
    {
        $tokens = preg_split('/[\s,;]+/', trim($receiverInput)) ?: [];
        $phones = [];

        foreach ($tokens as $token) {
            $phone = preg_replace('/\D+/', '', $token);
            if ($phone === '') {
                continue;
            }

            if (!preg_match('/^\d{10}$/', $phone)) {
                throw new \InvalidArgumentException("Numéro invalide: {$token}");
            }

            if (!$this->prefixModel->findPrefixByPhone($phone)) {
                throw new \InvalidArgumentException("Préfixe inconnu pour le numéro {$phone}.");
            }

            $phones[] = $phone;
        }

        return array_values(array_unique($phones));
    }

    private function calculateWithdrawalFee(int $amount): int
    {
        try {
            $feeService = new FeeService();
            return $feeService->calculate('retrait', $amount);
        } catch (\Throwable $e) {
            throw new \RuntimeException("Impossible de calculer les frais de retrait pour {$amount} Ar.");
        }
    }

    private function generateBatchReference(): string
    {
        return 'BATCH-' . date('YmdHis') . '-' . strtoupper(bin2hex(random_bytes(3)));
    }
}
