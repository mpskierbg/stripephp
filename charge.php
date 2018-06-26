<?php
    require_once('vendor/autoload.php');
    require_once('config/db.php');
    require_once('lib/pdo_db.php');
    require_once('models/Customer.php');
    require_once('models/Transaction.php');

    \Stripe\Stripe::setApiKey('sk_test_Fw1VXSskKWsmFFNcLsTUUtu0');

// Sanitize POST Array
$POST = filter_var_array($_POST, FILTER_SANITIZE_STRING);

$first_name = $POST['first_name'];
$last_name = $POST['last_name'];
$email = $POST['email'];
$token = $POST['stripeToken'];

// Create Customer in Stripe
$customer = \Stripe\Customer::create(array(
    "email" => $email,
    "source" => $token
));

// Charge Customer
$charge = \Stripe\Charge::create(array(
    "amount" => 5000,
    "currency" => "usd",
    "description" => "product descp",
    "customer" => $customer->id
));

// Customer Data
$customerData = [
    'id' => $charge->customer,
    'first_name' => $first_name,
    'last_name' => $last_name,
    'email' => $email
];

// Instantiate  Customer
$customer = new Customer();

// Add Customer to DB
$customer->addCustomer($customerData);

// Transaction Data
$transactionData = [
    'id' => $charge->id,
    'customer_id' => $charge->customer,
    'product' => $charge->description,
    'amount' => $charge->amount,
    'currency' => $charge->currency,
    'status' => $charge->status,
];

// Instantiate  transaction
$transaction = new Transaction();

// Add Customer to DB
$transaction->addTransaction($transactionData);

// Redirect to success
header('Location: success.php?tid='.$charge->id.'&product='.$charge->description);