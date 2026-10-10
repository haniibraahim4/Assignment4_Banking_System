<?php
function calculateDeposit($openingBalance, $amount) {
    return $openingBalance + $amount;
}


function calculateWithdrawal($openingBalance, $amount) {
    return $openingBalance - $amount;
}

function calculateTransactionFee($transactionType, $amount) {
    if ($transactionType == "Deposit") {
        return 0;
    } elseif ($transactionType == "Withdrawal" && $amount < 500) {
        return 2;
    } elseif ($transactionType == "Withdrawal" && $amount >= 500) {
        return 5;
    } else {
        return 0;
    }
}

function calculateInterest($accountType, $finalBalance, $duration) {
    if ($accountType == "Savings") {
        $rate = 0.05;
    } elseif ($accountType == "Current") {
        $rate = 0.02;
    } elseif ($accountType == "Business") {
        $rate = 0.03;
    } else {
        $rate = 0;
    }
    return $finalBalance * $rate * $duration;
}

function calculateTotalCharges($fee) {
    return $fee;
}

function calculateFinalBalance($openingBalance, $transactionType, $amount, $charges) {
    if ($transactionType == "Deposit") {
        return $openingBalance + $amount - $charges;
    } else {
        return $openingBalance - $amount - $charges;
    }
}

function checkAccountStatus($balance) {
    if ($balance >= 5000) {
        return "High Balance";
    } elseif ($balance >= 1000) {
        return "Medium Balance";
    } else {
        return "Low Balance";
    }
}

function calculateFutureBalance($finalBalance, $interest, $duration = 1) {
    return $finalBalance + $interest;
}
?>
