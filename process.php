<?php
require_once "functions.php";


$name = isset($_POST["customer_name"]) ? $_POST["customer_name"] : "";
$accountNumber = isset($_POST["account_number"]) ? $_POST["account_number"] : "";
$gender = isset($_POST["gender"]) ? $_POST["gender"] : "";
$accountType = isset($_POST["account_type"]) ? $_POST["account_type"] : "";
$openingBalance = isset($_POST["opening_balance"]) ? $_POST["opening_balance"] : "";
$transactionType = isset($_POST["transaction_type"]) ? $_POST["transaction_type"] : "";
$transactionAmount = isset($_POST["transaction_amount"]) ? $_POST["transaction_amount"] : "";
$duration = isset($_POST["duration"]) ? $_POST["duration"] : "";

$errors = "";


if (empty($name)) { $errors .= "Customer full name is required.<br>"; }
if (empty($accountNumber)) { $errors .= "Account number is required.<br>"; }
if (empty($gender)) { $errors .= "Please select gender.<br>"; }
if (empty($accountType)) { $errors .= "Please select account type.<br>"; }
if ($openingBalance === "" || $openingBalance < 0) { $errors .= "Opening balance must be zero or greater.<br>"; }
if (empty($transactionType)) { $errors .= "Please select transaction type.<br>"; }
if ($transactionAmount === "" || $transactionAmount <= 0) { $errors .= "Transaction amount must be greater than zero.<br>"; }
if ($duration === "" || $duration < 0 || $duration % 1 != 0) { $errors .= "Duration must be a non-negative whole number.<br>"; }
if ($accountType != "Savings" && $accountType != "Current" && $accountType != "Business") { $errors .= "Invalid account type.<br>"; }
if ($transactionType != "Deposit" && $transactionType != "Withdrawal") { $errors .= "Invalid transaction type.<br>"; }

$fileName = isset($_FILES["identification"]["name"]) ? $_FILES["identification"]["name"] : "";
$fileSize = isset($_FILES["identification"]["size"]) ? $_FILES["identification"]["size"] : 0;
$fileType = isset($_FILES["identification"]["type"]) ? $_FILES["identification"]["type"] : "";
$tempName = isset($_FILES["identification"]["tmp_name"]) ? $_FILES["identification"]["tmp_name"] : "";
$fileError = isset($_FILES["identification"]["error"]) ? $_FILES["identification"]["error"] : 4;

if (empty($fileName) || $fileError != 0) {
    $errors .= "Please select an identification document.<br>";
}
if ($fileSize > 2097152) {
    $errors .= "File must not be larger than 2 MB.<br>";
}


$fee = calculateTransactionFee($transactionType, $transactionAmount);
if ($transactionType == "Withdrawal" && ($transactionAmount + $fee) > $openingBalance) {
    $errors .= "Withdrawal amount plus fee cannot exceed opening balance.<br>";
}

$uploadStatus = "Not uploaded";
$savedName = "";
if ($errors == "") {
   
    if (!file_exists("uploads")) {
        mkdir("uploads");
    }


    $savedName = $fileName;
    if (file_exists("uploads/" . $savedName)) {
        $errors .= "A file with this name already exists. Rename your document and try again.<br>";
    } else {
        if (move_uploaded_file($tempName, "uploads/" . $savedName)) {
            $uploadStatus = "Successful";
        } else {
            $errors .= "File upload failed. Please try again.<br>";
        }
    }
}

if ($errors == "" && $uploadStatus == "Successful") {
    $totalCharges = calculateTotalCharges($fee);
    $finalBalance = calculateFinalBalance($openingBalance, $transactionType, $transactionAmount, $totalCharges);

    if ($transactionType == "Deposit") {
        $transactionBalance = calculateDeposit($openingBalance, $transactionAmount);
    } else {
        $transactionBalance = calculateWithdrawal($openingBalance, $transactionAmount);
    }

    if ($accountType == "Savings") {
        $interestRate = "5%";
    } elseif ($accountType == "Current") {
        $interestRate = "2%";
    } else {
        $interestRate = "3%";
    }

    $interest = calculateInterest($accountType, $finalBalance, $duration);
    $futureBalance = calculateFutureBalance($finalBalance, $interest, $duration);
    $status = checkAccountStatus($finalBalance);
} 
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Banking Transaction Receipt</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
<?php if ($errors != "") { ?>
    <h1>Transaction Error</h1>
    <div class="error"><?php echo $errors; ?></div>
    <p><a href="index.html">Back to Banking Form</a></p>
<?php } else { ?>
    <h1>Banking Transaction Receipt</h1>
    <div class="success">Transaction processed successfully.</div>
    <table>
      <tr><th>Customer Full Name</th><td><?php echo $name; ?></td></tr>
      <tr><th>Account Number</th><td><?php echo $accountNumber; ?></td></tr>
      <tr><th>Gender</th><td><?php echo $gender; ?></td></tr>
      <tr><th>Account Type</th><td><?php echo $accountType; ?></td></tr>
      <tr><th>Opening Balance</th><td>$<?php echo number_format($openingBalance, 2); ?></td></tr>
      <tr><th>Transaction Type</th><td><?php echo $transactionType; ?></td></tr>
      <tr><th>Transaction Amount</th><td>$<?php echo number_format($transactionAmount, 2); ?></td></tr>
      <tr><th>Transaction Fee</th><td>$<?php echo number_format($fee, 2); ?></td></tr>
      <tr><th>Total Banking Charges</th><td>$<?php echo number_format($totalCharges, 2); ?></td></tr>
      <tr><th>Final Balance</th><td>$<?php echo number_format($finalBalance, 2); ?></td></tr>
      <tr><th>Account Status</th><td><?php echo $status; ?></td></tr>
      <tr><th>Account Duration</th><td><?php echo $duration; ?> year(s)</td></tr>
      <tr><th>Interest Rate</th><td><?php echo $interestRate; ?></td></tr>
      <tr><th>Calculated Interest</th><td>$<?php echo number_format($interest, 2); ?></td></tr>
      <tr><th>Projected Future Balance</th><td>$<?php echo number_format($futureBalance, 2); ?></td></tr>
      <tr><th>Document Name</th><td><?php echo $savedName; ?></td></tr>
      <tr><th>File Type</th><td><?php echo $fileType; ?></td></tr>
      <tr><th>File Size</th><td><?php echo $fileSize; ?> bytes</td></tr>
      <tr><th>Upload Status</th><td><?php echo $uploadStatus; ?></td></tr>
    </table>
    <p><a href="index.html">Back to Banking Form</a></p>
<?php } ?>
</div>
</body>
</html>
